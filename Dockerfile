ARG MW_VERSION=1.35
FROM gesinn/docker-mediawiki-sqlite:${MW_VERSION}

ENV EXTENSION=QRLite
COPY composer*.json package*.json /var/www/html/extensions/$EXTENSION/

RUN cd extensions/$EXTENSION && \
    npm ci && \
    composer update

COPY . /var/www/html/extensions/$EXTENSION

RUN echo \
        "wfLoadExtension( '$EXTENSION' );\n" \
    >> LocalSettings.php
