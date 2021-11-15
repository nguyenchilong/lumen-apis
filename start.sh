#!/usr/bin/env sh

echo "Stop all services first"
docker-compose down

echo "Start all services again"
docker-compose up -d --build
