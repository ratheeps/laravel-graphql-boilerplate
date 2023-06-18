FROM nginx:1.23.1-alpine

# --------- Setup build options
ARG WORK_DIR=/var/www

# --------- Configuration
RUN rm /etc/nginx/conf.d/default.conf

# --------- Working and Start nginx
WORKDIR ${WORK_DIR}

# --------- Nginx process
CMD ["nginx", "-g", "daemon off;"]
