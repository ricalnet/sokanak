#!/usr/bin/env python3
"""
Script sederhana untuk menjalankan:
1. pio run --target clean
2. pio run --target upload
"""

import os
import sys

os.system("pio run --target clean")
os.system("pio run --target upload")
