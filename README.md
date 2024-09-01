# Image server

A simple image server with PHP, MySQL and Apache.

## Getting Started

To get started with the image server, follow these steps:

### Prerequisites

Ensure you have the following installed on your system:

- Docker
- Deno

### Installation

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/Hashory/image-server.git
   cd image-server
   ```

2. **Set Up the Docker Containers:** Start the Docker containers using Docker
   Compose.
   ```bash
   docker-compose up -d
   ```
   This command will set up a MySQL database and an Apache server running PHP.

3. **Initialize the Database:** The database is automatically initialized with
   the `init.sql` script located in `src/sql/`. The table for storing images
   will be created during this process.

### Usage

You can interact with the server using any HTTP client, such as `curl` or
Postman.

- **Upload an Image:** To upload an image, send a `POST` request to `/upload`
  with the image data in the body.
  ```bash
  curl -X POST --data-binary @path/to/your/image.png http://localhost:8080/upload
  ```

- **Download an Image:** To download an image, send a `GET` request to `/id`,
  where `id` is the image ID.
  ```bash
  curl http://localhost:8080/1 -o downloaded_image.png
  ```

- **Handle Errors:** The server handles various error scenarios, such as invalid
  requests and file size limits, returning appropriate HTTP status codes and
  messages.

### Testing

Unit tests are available and can be run using Deno. Ensure the server is running
before executing the tests.

Run the tests with the following command:

```bash
deno task test
```

### Security Considerations

- The `.htaccess` file contains rules to improve the security of your server by
  restricting access to sensitive files, such as `.env` and `.git`.
- Security headers are set to prevent MIME type sniffing and to control referrer
  policies.
- The server signature is disabled, and directory browsing is turned off.

### Customization

You can customize the server by modifying the Dockerfile, `.htaccess`, and other
configuration files to suit your needs.

### License

[MIT LICENSE](LICENSE)
