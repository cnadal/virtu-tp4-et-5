#!/bin/bash

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout ./nginx/certs/auth.little.bloomyindev.me.key \
  -out ./nginx/certs/auth.little.bloomyindev.me.crt \
  -subj "/CN=auth.little.bloomyindev.me"

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout ./nginx/certs/app.little.bloomyindev.me.key \
  -out ./nginx/certs/app.little.bloomyindev.me.crt \
  -subj "/CN=app.little.bloomyindev.me"