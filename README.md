# News Aggregator

### Requirements
1. Data aggregation and storage: Implement a backend system that fetches articles from selected data sources
   (choose at least 3 from the provided list) and stores them locally in a database. Ensure that the data is regularly
   updated from the live data sources.

2. API endpoints: Create API endpoints for the frontend application to interact with the backend. These endpoints
   should allow the frontend to retrieve articles based on search queries, filtering criteria (date, category, source), and
   user preferences (selected sources, categories, authors).

## 📝 Table of Contents

1. [Local Setup Instructions](#local-setup-instructions)  
2. [Implementation Architecture & Folder Structure](#architecture--folder-structure)  
3. [How It Works](#how-it-works)  
4. [API Documentation](#api-documentation)  
5. [Testing](#testing)
6. [License](#license)  

---

## Local Setup Instructions

### Prerequisites

- PHP 8.2+  
- Composer  
- A database (MySQL, PostgreSQL, or SQLite for local/dev)  
- API keys for NewsAPI, Guardian, NYTimes  

### Installation Steps

1. **Clone the repository**

   ```bash
   git clone https://github.com/Edu-Vin/news-aggregator.git
   cd news-aggregator

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Environment setup**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Fill in `.env` values, including database credentials and API keys:

   ```env
    NEWSAPI_KEY=...
    GUARDIAN_KEY=...
    NYTIMES_KEY=...
    
    NEWSAPI_BASE_URL=...
    GUARDIAN_BASE_URL=...
    NYTIMES_BASE_URL=...
   ```

4. **Database setup**

   If using SQLite:

   ```bash
   touch database/database.sqlite
   ```

   Then run:

   ```bash
   php artisan migrate --seed
   ```

5. **Start the application**

   ```bash
   php artisan serve
   ```

   The backend will by default be accessible at `http://localhost:8000`.

---

## Implementation Architecture & Folder Structure

```
├── app/
│   ├── Contracts/
│   │   ├── Article/
│   │   │   └── ArticleInterface.php
│   │   ├── Category/
│   │   │   └── CategoryInterface.php
│   │   └── Source/
│   │       └── SourceInterface.php
│
│   ├── Entities/
│   │   ├── Article/
│   │   │   └── ArticleEntity.php
│   │   ├── Category/
│   │   │   └── CategoryEntity.php
│   │   └── Source/
│   │       └── SourceEntity.php
│
│   ├── Repositories/
│   │   ├── Article/
│   │   │   └── ArticleRepository.php
│   │   ├── Category/
│   │   │   └── CategoryRepository.php
│   │   └── Source/
│   │       └── SourceRepository.php
│
│   ├── Services/
│   │   └── News/
│   │       ├── NewsApiService.php
│   │       ├── GuardianService.php
│   │       └── NYTimesService.php
│   │       └── BaseService.php

│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Article/
│   │   │   │   └── ArticleController.php
│   │   │   ├── Category/
│   │   │   │   └── CategoryController.php
│   │   │   └── Source/
│   │   │       └── SourceController.php
│   │   └── Resources/
│
│   ├── Jobs/
│   │   └── FetchNewsSourceJob.php
│
│   └── Console/
│       └── Commands/
│           └── FetchNewsCommand.php
│
├── database/
│   ├── factories/
│   │   ├── ArticleFactory.php
│   │   ├── CategoryFactory.php
│   │   └── SourceFactory.php
│   ├── migrations/
│   └── seeders/
│
├── routes/
│   ├── api.php
│   ├── web.php
│   └── console.php
│
├── tests/
│   ├── Feature/
│   │   ├── ArticleTest.php
│   │   ├── FetchNewsCommandTest.php

```

---

## How It Works

1. A console command or scheduled job triggers a fetch operation for all integrated sources.
2. Each service fetches and normalizes external articles.
3. Articles are categorized using keywords and saved through a repository layer.
4. API endpoints serve this data to the frontend using filters like date, author, category, etc.

---

## API Documentation

### Base URL

```
http://localhost:8000/api
```

---

### `GET /api/articles`

**Query Parameters:**

| Parameter  | Type   | Description                               |
|------------|--------|-------------------------------------------|
| `search`   | string | Search term for title/description/content |
| `category` | int    | Filter by category ID                     |
| `source`   | int    | Filter by source ID                       |
| `author`   | string | Filter by author                          |
| `from`     | string | Filter from date (YYYY-MM-DD)             |
| `to`       | string | Filter to date (YYYY-MM-DD)               |
| `per_page` | int    | Number of contents to retrieve per page   |


**Example Response:**

```json
{
  "data": [
    {
      "id": 1,
      "title": "Sample Title",
      "description": "Brief summary...",
      "author": "John Doe",
      "published_at": "2025-10-14",
      "url": "https://...",
      "source": {
        "id": 2,
        "name": "BBC"
      },
      "category": {
        "id": 1,
        "name": "Technology"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 10,
    "total": 15
  }
}
```

---

### `GET /api/categories`

```json
[
  { "id": 1, "name": "Technology" },
  { "id": 2, "name": "Health" }
]
```

---

### `GET /api/sources`

```json
[
  { "id": 1, "name": "BBC" },
  { "id": 2, "name": "The Guardian" }
]
```

---

## Testing

Run all tests:

```bash
php artisan test
```

---

## License

MIT License © Edu-Vin
