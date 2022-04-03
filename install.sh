#!/bin/bash
url="https://www.python.org/ftp/python/3.9.12/Python-3.9.12.tar.xz"


if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

sudo apt update
sudo apt install -y build-essential zlib1g-dev libncurses5-dev libgdbm-dev libnss3-dev libssl-dev libreadline-dev libffi-dev curl libsqlite3-dev libbz2-dev tesseract-ocr qpdf
wget $url -P /tmp/
cd /opt/ && tar -xf /tmp/Python-3.9.12.tar.xz
cd /opt/Python-3.9.12 && ./configure --enable-loadable-sqlite-extensions && make && sudo make install
python3.9 -m pip install ocrmypdf
python3.9 -m pip install tabula-py
