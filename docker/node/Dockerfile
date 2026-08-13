# Node.js контейнер для сборки/разработки Vue приложения
FROM node:22-alpine

WORKDIR /app

# По умолчанию пакеты ставятся через bind volume node_modules
RUN npm config set cache /tmp/npm-cache

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]