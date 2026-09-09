import os
import sys

def simple_deploy():
    """Deploy IoT - Sok!Anak"""
    print("Pilih deploy:")
    print("1. hx711")
    print("2. ultrasonic")
    
    choice = input("Pilihan: ")
    
    if choice == '1':
        if os.path.exists("hx711"):
            os.chdir("hx711")
            os.system(f"{sys.executable} deploy.py")
        else:
            print("Folder hx711 tidak ditemukan!")
            
    elif choice == '2':
        if os.path.exists("ultrasonic"):
            os.chdir("ultrasonic")
            os.system(f"{sys.executable} deploy.py")
        else:
            print("Folder ultrasonic tidak ditemukan!")
    else:
        print("Pilihan tidak valid!")

if __name__ == "__main__":
    simple_deploy()