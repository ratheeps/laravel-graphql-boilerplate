FROM mysql:8.0.30

# --------- Update to latest MySQL GPG key
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 467B942D3A79BD29

# --------- Install dependancies
RUN apt-get update && apt-get install -y \
    procps \
    ruby \
    rsyslog \
    && gem install masking

# --------- Log Directory
RUN mkdir -p /var/log/mysql \
    && touch /var/log/mysql/mysql.log \
    && touch /var/log/mysql/mysql-slow.log \
    && touch /var/log/mysql/mysql.err.log \
    && chown -R mysql:mysql /var/log/mysql \
    && find /var/log/mysql -type f -exec chmod 755 {} \;

# --------- Main command
CMD ["mysqld"]
