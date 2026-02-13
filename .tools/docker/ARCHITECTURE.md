# Docker Architecture

**Docker Project Name**: `employeeslist-project`

This document describes the architecture and component relationships of the Dockerized Employees List application.

## System Architecture Diagram

```mermaid
graph TB
    subgraph "Host Machine"
        Browser[Web Browser]
        DBClient[Database Client<br/>MySQL Workbench/DBeaver]
    end

    subgraph "Docker Network: employeeslist-network"
        subgraph "App Container (employeeslist-app)"
            Nginx[Nginx Web Server<br/>Port 80]
            PHP[PHP-FPM 8.2]
            Laravel[Laravel Application]
            Supervisor[Supervisor<br/>Process Manager]
        end

        subgraph "MySQL Container (employeeslist-mysql)"
            MySQL[(MySQL 8.0 Database<br/>employees DB)]
            MySQLData[(/var/lib/mysql)]
        end

        subgraph "PHPMyAdmin Container"
            PMA[PHPMyAdmin<br/>Port 80]
        end
    end

    subgraph "Docker Volumes"
        Vol1[(mysql_data)]
        Vol2[(storage_data)]
    end

    Browser -->|http://localhost:8000| Nginx
    Browser -->|http://localhost:8080| PMA
    DBClient -->|localhost:3306| MySQL

    Nginx --> PHP
    PHP --> Laravel
    Laravel -->|PDO/Eloquent| MySQL
    PMA -->|MySQL Protocol| MySQL
    Supervisor --> PHP
    Supervisor --> Nginx

    MySQL --> MySQLData
    MySQLData --> Vol1
    Laravel --> Vol2

    style Nginx fill:#90EE90
    style PHP fill:#90EE90
    style Laravel fill:#FF6B6B
    style MySQL fill:#4169E1
    style PMA fill:#FFA500
    style Vol1 fill:#DDA0DD
    style Vol2 fill:#DDA0DD
```

## Container Communication Flow

```mermaid
sequenceDiagram
    participant User
    participant Nginx
    participant PHP
    participant Laravel
    participant MySQL

    User->>Nginx: HTTP Request (port 8000)
    Nginx->>PHP: Forward to PHP-FPM (port 9000)
    PHP->>Laravel: Execute PHP Code
    Laravel->>MySQL: Database Query (port 3306)
    MySQL-->>Laravel: Query Results
    Laravel-->>PHP: Response Data
    PHP-->>Nginx: HTML/JSON Response
    Nginx-->>User: HTTP Response
```

## Startup Sequence

```mermaid
sequenceDiagram
    participant DC as Docker Compose
    participant MySQL
    participant App
    participant Init as Init Script

    DC->>MySQL: 1. Start MySQL Container
    MySQL->>MySQL: 2. Initialize Database
    Init->>MySQL: 3. Import employees.sql
    MySQL->>MySQL: 4. Health Check
    MySQL-->>DC: 5. Container Ready (healthy)

    DC->>App: 6. Start App Container<br/>(waits for MySQL health)
    App->>App: 7. Copy .env file
    App->>App: 8. Generate APP_KEY
    App->>App: 9. Run migrations
    App->>App: 10. Cache configuration
    App->>App: 11. Start Supervisor
    App->>App: 12. Start PHP-FPM
    App->>App: 13. Start Nginx
    App-->>DC: 14. Application Ready

    Note over App,MySQL: App can now serve requests
```

## Data Flow Architecture

```mermaid
flowchart LR
    subgraph "External"
        A[User Request]
        B[Database Client]
    end

    subgraph "App Container"
        C[Nginx:80]
        D[PHP-FPM:9000]
        E[Laravel App]
        F[Storage Volume]
    end

    subgraph "Database Container"
        G[MySQL:3306]
        H[Data Volume]
    end

    subgraph "Management"
        I[PHPMyAdmin:80]
    end

    A -->|:8000| C
    C --> D
    D --> E
    E -->|Read/Write| F
    E -->|Queries| G
    G -->|Persist| H
    B -->|:3306| G
    I -->|Manage| G

    style A fill:#E1F5FF
    style B fill:#E1F5FF
    style C fill:#90EE90
    style D fill:#90EE90
    style E fill:#FF6B6B
    style F fill:#DDA0DD
    style G fill:#4169E1
    style H fill:#DDA0DD
    style I fill:#FFA500
```

## File System Structure

```mermaid
graph TD
    A[Host: .tools/docker] --> B[Dockerfile]
    A --> C[docker-compose.yml]
    A --> D[Configuration Files]

    D --> D1[nginx.conf]
    D --> D2[php.ini]
    D --> D3[supervisord.conf]
    D --> D4[mysql-init.sh]

    E[Host: backend/] --> F[App Volume Mount]
    F --> G[Laravel Application]

    H[Host: database/] --> I[Database Init Volume]
    I --> J[employees.sql]

    K[Docker Volume: mysql_data] --> L[MySQL Data Persistence]
    M[Docker Volume: storage_data] --> N[Laravel Storage]

    style A fill:#FFE5B4
    style E fill:#FFE5B4
    style H fill:#FFE5B4
    style K fill:#DDA0DD
    style M fill:#DDA0DD
    style G fill:#FF6B6B
    style L fill:#4169E1
```

## Network Architecture

```mermaid
graph TB
    subgraph "Host Network"
        H1[Host Port 8000]
        H2[Host Port 8080]
        H3[Host Port 3306]
    end

    subgraph "Docker Bridge Network: employeeslist-network"
        subgraph "App Container"
            A1[Nginx:80]
        end

        subgraph "MySQL Container"
            M1[MySQL:3306]
        end

        subgraph "PHPMyAdmin"
            P1[PHPMyAdmin:80]
        end
    end

    H1 -.->|Port Mapping| A1
    H2 -.->|Port Mapping| P1
    H3 -.->|Port Mapping| M1

    A1 -->|Internal Network| M1
    P1 -->|Internal Network| M1

    style H1 fill:#E1F5FF
    style H2 fill:#E1F5FF
    style H3 fill:#E1F5FF
    style A1 fill:#90EE90
    style M1 fill:#4169E1
    style P1 fill:#FFA500
```

## Deployment Process

```mermaid
flowchart TD
    Start([User runs docker-compose up]) --> Check{Docker Running?}
    Check -->|No| Error1[Error: Start Docker]
    Check -->|Yes| Pull[Pull/Build Images]

    Pull --> CreateNet[Create Network]
    CreateNet --> StartMySQL[Start MySQL Container]
    StartMySQL --> WaitMySQL[Wait for Health Check]
    WaitMySQL --> ImportDB[Import Database]
    ImportDB --> MySQLReady{MySQL Ready?}

    MySQLReady -->|No| Error2[Error: Check logs]
    MySQLReady -->|Yes| StartApp[Start App Container]

    StartApp --> ConfigApp[Configure Laravel]
    ConfigApp --> Migrate[Run Migrations]
    Migrate --> Cache[Cache Config]
    Cache --> StartServ[Start Services]
    StartServ --> StartPMA[Start PHPMyAdmin]
    StartPMA --> Done([Ready: localhost:8000])

    style Start fill:#90EE90
    style Done fill:#90EE90
    style Error1 fill:#FF6B6B
    style Error2 fill:#FF6B6B
    style StartMySQL fill:#4169E1
    style StartApp fill:#FF6B6B
    style StartPMA fill:#FFA500
```

## Container Dependencies

```mermaid
graph TD
    A[MySQL Container] --> B[App Container]
    A --> C[PHPMyAdmin Container]

    B1[mysql_data Volume] --> A
    B2[storage_data Volume] --> B

    N[employeeslist-network] --> A
    N --> B
    N --> C

    H1[Health Check] --> A
    H1 -.->|Waits for| B

    style A fill:#4169E1
    style B fill:#FF6B6B
    style C fill:#FFA500
    style B1 fill:#DDA0DD
    style B2 fill:#DDA0DD
    style N fill:#E1F5FF
    style H1 fill:#90EE90
```

## Environment Configuration Flow

```mermaid
flowchart LR
    A[.env.docker Template] -->|Copy| B[backend/.env]
    C[docker-compose.yml] -->|Environment Variables| D[MySQL Container]
    C -->|Environment Variables| E[App Container]

    B -->|Read by| E
    D -->|DB Connection| E

    F[php.ini] -->|Configure| G[PHP Runtime]
    H[nginx.conf] -->|Configure| I[Nginx Server]

    G --> E
    I --> E

    style A fill:#FFE5B4
    style B fill:#FFE5B4
    style C fill:#FFE5B4
    style D fill:#4169E1
    style E fill:#FF6B6B
```

## Scaling Possibilities

```mermaid
graph TB
    LB[Load Balancer<br/>Nginx/Traefik]

    LB --> App1[App Container 1]
    LB --> App2[App Container 2]
    LB --> App3[App Container 3]

    App1 --> DB[(MySQL Primary)]
    App2 --> DB
    App3 --> DB

    DB --> Replica1[(MySQL Replica 1)]
    DB --> Replica2[(MySQL Replica 2)]

    Cache[Redis Cache] --> App1
    Cache --> App2
    Cache --> App3

    Queue[Queue Worker] --> App1

    style LB fill:#90EE90
    style App1 fill:#FF6B6B
    style App2 fill:#FF6B6B
    style App3 fill:#FF6B6B
    style DB fill:#4169E1
    style Replica1 fill:#4169E1
    style Replica2 fill:#4169E1
    style Cache fill:#FFA500
    style Queue fill:#DDA0DD
```

## Legend

- 🟢 **Green**: Web Servers / Entry Points
- 🔴 **Red**: Application Containers
- 🔵 **Blue**: Database Containers
- 🟠 **Orange**: Management Tools
- 🟣 **Purple**: Storage / Volumes
- ⚪ **Light Blue**: External Access Points

---

## Notes

- All containers run on the same Docker bridge network (`employeeslist-network`)
- Containers communicate using container names as hostnames
- Data persists in Docker volumes even when containers are removed
- Port mappings allow external access from the host machine
- Health checks ensure proper startup order
- Supervisor manages multiple processes in the app container
