## Install


#### Step1
Create ./client/.env file and insert the content of .client/.env.dist.

#### Step2
Set correct MAILER_URL, MAILER_USER_EMAIL, MAILER_USER_NAME values

#### Step3

```bash
$ docker-compose up -d
```

## Addresses:
- Quizzes interface: [https://localhost](https://localhost)
- Admin interface: [https://localhost:444](https://localhost:444)

## Create an admin:
```bash
$ docker-compose exec php bin/console app:create-admin
```
