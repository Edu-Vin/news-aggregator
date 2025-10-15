# News Aggregator
This was implemented using repository pattern.
### Requirements
1. Data aggregation and storage: Implement a backend system that fetches articles from selected data sources
   (choose at least 3 from the provided list) and stores them locally in a database. Ensure that the data is regularly
   updated from the live data sources.

2. API endpoints: Create API endpoints for the frontend application to interact with the backend. These endpoints
   should allow the frontend to retrieve articles based on search queries, filtering criteria (date, category, source), and
   user preferences (selected sources, categories, authors).

## 📝 Table of Contents

1. [Local Setup Instructions](#local-setup-instructions)   
2. [How It Works](#how-it-works)  
3. [API Documentation](#api-documentation)  
4. [Testing](#testing)
5. [License](#license)  

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
    API_KEY=
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

## How It Works

1. A console command or scheduled job triggers a fetch operation for all integrated sources.
2. Each service fetches and normalizes external articles.
3. Articles are categorized using keywords and saved through a repository layer.
4. API endpoints serve this data to the frontend using filters like date, author, category, etc and also user preferences.

---

## API Documentation
The postman_collection.json which is the postman collection export is included in the codebase

### Base URL

```
http://localhost:8000/api
```
### Postman Documentation
https://documenter.getpostman.com/view/9119505/2sB3QNoThR
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


---

## Testing

Run all tests:

```bash
php artisan test
```

---

## License

MIT License © Edu-Vin
