FROM php:7.4-cli-buster

WORKDIR "/opt/php"

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && \
    apt-get install -y unzip autoconf zlib1g-dev && \
    pecl channel-update pecl.php.net && \
    pecl install grpc && \
    pecl install mongodb && \
    mkdir -p /tmp/protoc && \
    curl -L https://github.com/google/protobuf/releases/download/v3.2.0/protoc-3.2.0-linux-x86_64.zip > /tmp/protoc/protoc.zip && \
    cd /tmp/protoc && \
    unzip protoc.zip && \
    cp /tmp/protoc/bin/protoc /usr/local/bin && \
    cd /tmp && \
    rm -r /tmp/protoc && \
    docker-php-ext-install bcmath && \
    docker-php-ext-enable mongodb grpc && \
    docker-php-source delete && \
    rm -r /tmp/* /var/cache/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
