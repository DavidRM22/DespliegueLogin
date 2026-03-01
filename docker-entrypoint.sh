#!/usr/bin/env sh
set -e

PORT="${PORT:-10000}"

# Apache debe escuchar en 0.0.0.0:$PORT para Render
# Ajusta ports.conf y el VirtualHost
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf || true
sed -i "s/:80>/:${PORT}>/g" /etc/apache2/sites-available/000-default.conf || true

apache2-foreground
