# my-money
- My Money, My Rules : A new amazing wallet!
- This is a digital wallet for clients and shopkeepers

### How to Run Locally
 **Follow the steps below to run the application locally on your machine.**

#### Requirements:
- Docker installed on your machine.
- Git to clone the repository.

#### Installation

- **Clone** this repository: 
```
git clone https://github.com/hart42/my-money.git
```

- Build the docker container,
- Start the container
```
docker-compose build
docker-compose up -d
```

- Enter the container and install the dependencies:

```
docker exec -it my-money-laravel bash

composer install
chown -R www-data:www-data /var/www
chmod -R 755 /var/www
```

- Run the migrations, 
- Clear the caches:

```
php artisan migrate

php artisan config:cache
php artisan route:cache
php artisan cache:clear
```

#### Execution
- Run the test with the following command:
```
php artisan test
```

## **Endpoints**

#### `POST` `/create-client`

- Create a new Client and a new Account with initial balance as `0.00`
- For both types of user, we need the `Full Name`, `CPF` or `CNPJ`, `e-mail` and `Password`. `CPF/CNPJ` and `e-mail` addresses must be `unique` in the system. Therefore, your system should only allow one registration with the same CPF/CNPJ or e-mail address;
- If `CPF` is sent, `CNPJ` is not required. If `CNPJ` is sent, `CPF` is not required.
- If only `CPF`, or `CPF` and `CNPJ` are sent but without `account_type`, the account will be created as a `client`, if `only CNPJ` is sent the account will be `shop`.

####   **Request** 
- The body must have an object with the following properties (respecting these names):
    - "full_name": "Harry Pother", (string)
    - "cpf": "12345678911", (string)
    - "cnpj": "12345678911234", (string)
    - "email": "test@email.com", (email)
    - "password": "password", (string)
    - "shop_name": "hogwarts legacy", (string)
    - "account_type": 'client' (string)
    - account_type is not required but if send must be in (**client**, **shop**)

-   **Response**  
    If **successful**, returns HTTP Status 201 and a json in the body:

#### **Examples of responses**
- If **successful**
```json
// HTTP Status 201
    {
        "message": "Account created successfully!",
        "account": {
            "account_id": 12,
            "balance": "0.00",
            "account_type": "shop"
        },
        "client": {
            "client_id": 14,
            "full_name": "Luck Skywallker",
            "email": "test@email.com"
        }
    }
```
- If it **fails** due to a missing mandatory parameter
- "The **name of parameter missing** field is required."
```json
// HTTP Status 400
    {
        "error_message": "The full name field is required."
    }
```

#### `POST` `/deposit`

- If you it pass authentication, it will `Deposit` a positive value in an `Account`
- payee = account_id
- value =  float, positive

####   **Request** 
- The body must have an object with the following properties (respecting these names):
    - "payee": 1, (int)
    - "value": 42.00 (float)

-   **Response**  
    If **successful**, returns HTTP Status 200 and a json in the body:

#### **Examples of responses**
- If **successful**
```json
// HTTP Status 200
{
	"message": "successfully deposited!",
	"account": {
		"account_id": 377,
		"balance": "422.00",
		"account_type": "client"
	}
}
```
- If it **fails** due to a missing mandatory parameter
- "The **name of parameter missing** field is required."
```json
// HTTP Status 400
    {
        "error_message": "The full name field is required."
    }
```

#### `POST` `/withdraw`

- If you it pass authentication, it will `withdraw` a positive value of an `Account`
- payer = account_id
- value =  float, positive

####   **Request** 
- The body must have an object with the following properties (respecting these names):
    - "payer": 1, (int)
    - "value": 42.00 (float)

-   **Response**  
    If **successful**, returns HTTP Status 200 and a json in the body:

#### **Examples of responses**
- If **successful**
```json
// HTTP Status 200
{
	"message": "successfully withdraw!",
	"account": {
		"account_id": 377,
		"balance": "400.00",
		"account_type": "client"
	}
}
```
- If it **fails** due to a missing mandatory parameter
- "The **name of parameter missing** field is required."
```json
// HTTP Status 400
    {
        "error_message": "The full name field is required."
    }
```

#### `POST` `/transfer`

- If you it pass authentication, it will `transfer` from `client` Account to another `client` or `shop` Account.
- The `value` is a positive value that will be subtracted from the `payer`'s account and added to the `payee`'s account.
- payer = account_id, must be an `account_type` **client**
- payee = account_id
- value =  float, positive

####   **Request** 
- The body must have an object with the following properties (respecting these names):
    - "payer": 1, (int)
    - "payee": 12, (int)
    - "value": 42.00 (float)

-   **Response**  
    If **successful**, returns HTTP Status 200 and a json in the body:

#### **Examples of responses**
- If **successful**
```json
// HTTP Status 200
{
	"message": "successfully transfer!",
	"transfer": {
		"payer": 377,
		"payee": 12,
		"amount": "22.00"
	}
}
```
- If it **fails** due to a missing mandatory parameter
- "The **name of parameter missing** field is required."
```json
// HTTP Status 400
    {
        "error_message": "The full name field is required."
    }
```
- If it **fails** due the payer its a `shop` account
```json
// HTTP Status 400
{
	"message": "payer cannot be a shopkeeper"
}
```