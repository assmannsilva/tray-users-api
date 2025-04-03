# Tray Users API

Este repositório contém a API Tray Users, um projeto Laravel configurado para autenticação utilizando o Google OAuth.

## Instalação

Siga os passos abaixo para configurar e iniciar a aplicação:

### 1. Clone o repositório
```sh
git clone https://github.com/assmannsilva/tray-users-api.git
cd tray-users-api
```

### 2. Configure o ambiente
Copie o arquivo de exemplo do ambiente:
```sh
cp .env.example .env
```

O arquivo `.env` contém vários valores padrões. A única configuração obrigatória é a de e-mail, que utiliza o SMTP do Google. Edite o arquivo `.env` e forneça as credenciais SMTP corretamente.

### 3. Suba os containers Docker
```sh
docker-compose up --build -d
```

### 4. Instale as dependências do Laravel
```sh
docker exec -it app composer install
```

### 5. Execute as migrações do banco de dados
```sh
docker exec -it app php artisan migrate
```

### 6. Configure as credenciais do Google
O arquivo `google-credentials` deve ser colocado na pasta `storage/app/private` para que a autenticação via Google OAuth funcione corretamente.

