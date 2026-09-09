#include <Arduino.h>
#include <HX711_ADC.h>
#include <WiFi.h>
#include <PubSubClient.h>

#if defined(ESP8266) || defined(ESP32)
#include <EEPROM.h>
#endif

const char* ssid = "your_ssid";                   // sesuaikan
const char* password = "your_wifi_password";      // sesuaikan

const char* mqtt_server = "ip_addr_or_domain";    // sesuaikan
const char* mqtt_topic  = "iot/sensor_berat";
const char* mqtt_username = "mqtt_username";      // sesuaikan
const char* mqtt_password = "mqtt_password";      // sesuaikan
const int   mqtt_port = 1883;

const int HX711_dout = 4;   // sesuaikan
const int HX711_sck  = 5;   // sesuaikan

HX711_ADC LoadCell(HX711_dout, HX711_sck);

const int calVal_eepromAdress = 0;
const float CALIBRATION_MAGIC = 12345.67;

struct CalibrationData {
  float magic;
  float calFactor;
};

const float GRAM_TO_KG = 0.001;

unsigned long t = 0;
unsigned long lastMqttPublish = 0;
const unsigned long mqttPublishInterval = 2000;

WiFiClient espClient;
PubSubClient client(espClient);

void setup_wifi();
void reconnect();
void publishWeight(float weightKg);

void calibrate();
void changeSavedCalFactor();
bool loadCalibrationFromEEPROM();

void setup() {
  Serial.begin(115200);
  setup_wifi();

  client.setServer(mqtt_server, mqtt_port);
  client.setBufferSize(64);

  LoadCell.begin();
  LoadCell.setReverseOutput();

  unsigned long stabilizingtime = 2000;
  boolean _tare = true;
  LoadCell.start(stabilizingtime, _tare);

  if (LoadCell.getTareTimeoutFlag() || LoadCell.getSignalTimeoutFlag()) {
    Serial.println("HX711 timeout, check wiring!");
    while (1);
  }

  bool calibrated = loadCalibrationFromEEPROM();
  if (!calibrated) {
    Serial.println("No calibration found, starting calibration...");
    calibrate();
  }

  Serial.println("System ready.");
}

void loop() {
  static boolean newDataReady = false;

  if (!client.connected()) reconnect();
  client.loop();

  if (LoadCell.update()) newDataReady = true;

  if (newDataReady) {
    float weightG = LoadCell.getData();
    float weightKg = weightG * GRAM_TO_KG;

    Serial.print("Weight: ");
    Serial.print(weightKg, 4);
    Serial.println(" kg");

    newDataReady = false;
    t = millis();
  }

  if (millis() - lastMqttPublish > mqttPublishInterval) {
    float weightKg = LoadCell.getData() * GRAM_TO_KG;
    publishWeight(weightKg);
    lastMqttPublish = millis();
  }

  if (Serial.available()) {
    char cmd = Serial.read();
    if (cmd == 't') LoadCell.tareNoDelay();
    else if (cmd == 'r') calibrate();
    else if (cmd == 'c') changeSavedCalFactor();
  }

  if (LoadCell.getTareStatus()) {
    Serial.println("Tare complete");
  }
}

bool loadCalibrationFromEEPROM() {
  CalibrationData data;

#if defined(ESP8266) || defined(ESP32)
  EEPROM.begin(512);
#endif
  EEPROM.get(calVal_eepromAdress, data);

  if (data.magic == CALIBRATION_MAGIC && data.calFactor != 0) {
    LoadCell.setCalFactor(data.calFactor);
    Serial.print("Calibration loaded: ");
    Serial.println(data.calFactor);
    return true;
  }

  Serial.println("EEPROM calibration invalid");
  return false;
}

void calibrate() {
  Serial.println("\n=== CALIBRATION START ===");
  Serial.println("Remove all load.");
  Serial.println("Send 't' to tare.");

  while (!LoadCell.getTareStatus()) {
    LoadCell.update();
    if (Serial.available() && Serial.read() == 't') {
      LoadCell.tareNoDelay();
    }
  }

  Serial.println("Place known mass (GRAM) and send value:");

  float known_mass = 0;
  while (known_mass == 0) {
    LoadCell.update();
    if (Serial.available()) {
      known_mass = Serial.parseFloat();
    }
  }

  LoadCell.refreshDataSet();
  float newCal = LoadCell.getNewCalibration(known_mass);

  Serial.print("New calibration factor: ");
  Serial.println(newCal);
  Serial.println("Save to EEPROM? y/n");

  while (true) {
    if (Serial.available()) {
      char c = Serial.read();
      if (c == 'y') {
        CalibrationData data = {CALIBRATION_MAGIC, newCal};
#if defined(ESP8266) || defined(ESP32)
        EEPROM.put(calVal_eepromAdress, data);
        EEPROM.commit();
#endif
        Serial.println("Calibration saved.");
        break;
      }
      if (c == 'n') {
        Serial.println("Calibration NOT saved.");
        break;
      }
    }
  }

  Serial.println("=== CALIBRATION END ===");
}

void changeSavedCalFactor() {
  Serial.print("Current CalFactor: ");
  Serial.println(LoadCell.getCalFactor());
  Serial.println("Send new calibration value:");

  float newCal = 0;
  while (newCal == 0) {
    if (Serial.available()) {
      newCal = Serial.parseFloat();
    }
  }

  LoadCell.setCalFactor(newCal);
  Serial.println("Save to EEPROM? y/n");

  while (true) {
    if (Serial.available()) {
      char c = Serial.read();
      if (c == 'y') {
        CalibrationData data = {CALIBRATION_MAGIC, newCal};
#if defined(ESP8266) || defined(ESP32)
        EEPROM.put(calVal_eepromAdress, data);
        EEPROM.commit();
#endif
        Serial.println("Saved.");
        break;
      }
      if (c == 'n') {
        Serial.println("Not saved.");
        break;
      }
    }
  }
}

void setup_wifi() {
  Serial.print("Connecting to WiFi");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected");
}

void reconnect() {
  while (!client.connected()) {
    Serial.print("Connecting MQTT...");
    if (client.connect("HX711_Client", mqtt_username, mqtt_password)) {
      Serial.println("connected");
    } else {
      Serial.println("failed, retrying...");
      delay(5000);
    }
  }
}

void publishWeight(float weightKg) {
  char payload[16];
  snprintf(payload, sizeof(payload), "%.2f", weightKg);
  
  if (client.publish("iot/sensor_berat", payload, true)) {
    Serial.print("MQTT published: ");
    Serial.print(payload);
    Serial.println(" kg");
  } else {
    Serial.println("MQTT publish failed!");
  }
}