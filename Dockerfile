FROM node:alpine
COPY package.json package.json
RUN npm install

# Add your source files
COPY . .
EXPOSE 3000
CMD ["nmp", "start"]
