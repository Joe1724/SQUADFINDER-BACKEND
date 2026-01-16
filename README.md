# SquadFinder - Backend API

The native PHP backend API that powers the SquadFinder mobile application. This API handles user authentication, data persistence, and communication with the official **Riot Games API**.

## 🔌 API Features
* **Riot Games Integration:** Consumes `ACCOUNT-V1`, `SUMMONER-V4`, `LEAGUE-V4`, and `VAL-MATCH-V1` endpoints.
* **Custom Server Logic:** Implements a "Global Scanner" algorithm to determine a player's active server across 11+ regions (PH2, SG2, TW2, VN2, TH2, NA1, KR, EUW1, BR1, LA1, LA2, OC1).
* **Multi-Queue Support:** Fetches both SOLO/DUO and FLEX rank data with win rates and LP tracking.
* **User Management:** Handles Registration, Login, Profile Updates, and Avatar Uploads using standard JSON payloads.
* **Data Persistence:** MySQL database integration for storing user credentials and verified game stats.
* **Rate Limit Handling:** Built-in delays and error handling to prevent API throttling.

## 🛠 Tech Stack
* **Language:** Native PHP 8.0+
* **Database:** MySQL / MariaDB
* **Server:** Apache / Nginx (Laragon)
* **Format:** RESTful JSON API
* **External APIs:** Riot Games API (Personal API Key)

## ⚙️ Installation

### Prerequisites
* XAMPP / WAMP / LAMP / Laragon Stack installed.
* PHP 8.0 or higher with cURL extension enabled.
* MySQL / MariaDB database server.
* Riot Games Developer Account (for API key).

### Setup
1.  **Clone the repository**
    ```bash
    git clone https://github.com/Joe1724/SQUADFINDER-BACKEND.git
    cd SQUADFINDER-BACKEND
    ```

2.  **Database Configuration**
    * Import the `database.sql` file (if provided) into phpMyAdmin.
    * Configure `config/db_connect.php` with your local credentials:
        ```php
        $host = "localhost";
        $db_name = "squadfinder_db";
        $username = "root";
        $password = "";
        ```

3.  **Riot API Key Configuration**
    * Get your Personal API Key from [Riot Developer Portal](https://developer.riotgames.com/)
    * **Important:** Personal API keys expire every 24 hours and must be regenerated daily.
    * Update the API key in your chosen endpoint:
        ```php
        // In api/link_riot_v2.php (recommended)
        $api_key = "RGAPI-YOUR-KEY-HERE";
        ```

4.  **Run Server**
    * Place the project folder in your `htdocs` (XAMPP), `www` (WAMP), or Laragon's `www` directory.
    * Access via: `http://localhost/squadfinder/api/`

## 📡 API Endpoints

### Authentication
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/register.php` | Creates a new user account. |
| `POST` | `/login.php` | Authenticates user credentials. |

### Profile Management
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/get_profile.php?user_id={id}` | Fetches user profile with Riot stats. |
| `POST` | `/update_profile.php` | Updates user bio, rank, and role. |
| `POST` | `/upload_avatar.php` | Uploads user profile picture. |

### Riot Integration (Choose One)
| Method | Endpoint | Description | Status |
| :--- | :--- | :--- | :--- |
| `POST` | `/link_riot_v2.php` | **Recommended** - Official API with rate limiting & multi-server scan. | ✅ Active |
| `POST` | `/link_all_riot.php` | Original implementation with global scanner. | ✅ Active |
| `POST` | `/link_riot_simple.php` | Simplified version for SEA region only. | ⚠️ Backup |
| `POST` | `/link_riot_scrape.php` | Web scraping fallback (uses League of Graphs). | ⚠️ Fallback |

### Matchmaking
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/feed.php?user_id={id}` | Returns potential matches. |
| `POST` | `/swipe.php` | Records swipe action (like/pass). |
| `GET` | `/get_matches.php?user_id={id}` | Returns mutual matches. |

### Messaging
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/get_messages.php?user_id={id}&match_id={id}` | Fetches chat history. |
| `POST` | `/send_message.php` | Sends a message to a match. |

### Testing
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/test_riot_api.php` | Tests if Riot API key is valid and working. |

## 🎮 Riot API Integration Details

### Supported Games
- **League of Legends** - SOLO/DUO and FLEX ranks with LP and win rates
- **Valorant** - Competitive rank and K/D ratio (limited support)

### Server Coverage
The API automatically scans these regions to find player accounts:
- **SEA:** SG2, PH2, TW2, VN2, TH2
- **Americas:** NA1, BR1, LA1, LA2
- **Europe:** EUW1, EUN1
- **Asia:** KR, JP1
- **Oceania:** OC1

### Data Returned
```json
{
  "status": 200,
  "message": "Linked successfully on SG2",
  "data": {
    "server": "SG2",
    "lol": {
      "solo": {
        "rank": "CHALLENGER I 1909LP",
        "winrate": 58
      },
      "flex": {
        "rank": "DIAMOND II",
        "winrate": 62
      }
    },
    "val": {
      "rank": "Unranked",
      "kd": "0.00"
    }
  }
}
```

## 🔧 Troubleshooting

### Common Issues

**403 Forbidden Error**
- Your API key has expired (Personal keys expire every 24 hours)
- Solution: Get a new key from [developer.riotgames.com](https://developer.riotgames.com/)

**429 Rate Limited**
- Too many requests in a short time
- Solution: Wait 2 minutes or use `link_riot_v2.php` which has built-in delays

**Rank Shows "Unranked"**
- API key is expired
- Player hasn't played ranked games this season
- Wrong server region
- Solution: Update API key and re-link account

**Database Connection Failed**
- Check `config/db_connect.php` credentials
- Ensure MySQL service is running
- Verify database name exists

## 📝 Notes

- **API Key Maintenance:** Personal API keys must be regenerated daily from the Riot Developer Portal.
- **Rate Limits:** Personal keys allow 20 requests/second and 100 requests/2 minutes.
- **Production Keys:** For production use, apply for a Production API Key which doesn't expire.
- **CORS:** All endpoints include CORS headers for cross-origin requests from mobile apps.

## 🔗 Related Repositories
- Frontend (React Native): [SQUADFINDER-MOBILE](https://github.com/Joe1724/SQUADFINDER-MOBILE)

## 📄 License
MIT License - Feel free to use this project for learning and development.