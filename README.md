# My homepage based on grow

## Docker

1. docker build --build-arg grow_version=0.7.6 -t grow .
2. docker run --rm -p 8080:8080 grow:latest run /src --host 0.0.0.0
