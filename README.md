# BetWinner Super Admin & Mobile Backend API

Welcome to the **BetWinner Backend & Super Admin Panel**, built with Laravel 11 and tailored for sports betting insights, tipster management, and seamless React Native mobile app synchronization.

---

## Architecture & Role System

The platform enforces a strict three-tier role-based access model:

1. **Super Admin (`super_admin`)**:
   - Manages and approves tipster registrations, verification badges, and account suspensions.
   - Settle betslips outcomes (`active`, `won`, `lost`, `void`).
   - Manages the **Auto-Scrolling Ads Board** with 3-second rotator frequency.
   - Publishes **Featured Big Matches** with odds, venue analytics, and key insights.
   - Creates and oversees interactive **Community Match Voting Polls**.
   - Manages **VIP Subscription Tiers** (Daily, Weekly, Monthly, Season passes) and grants/revokes access.
   - Audits the **Financial Ledger** for Vodacom M-Pesa, Airtel Money, and Tigo Pesa payments in Tanzanian Shillings (**Tshs**).
   - Configures real bookmaker integrations (**Betika**, **SportyBet**, **1xBet**, **MozzartBet**, **Betway**).

2. **Tipster (`tipster`)**:
   - Must be verified and approved by the Super Admin before publishing.
   - Accesses a dedicated **Tipster Publishing Portal** (`/tipster/dashboard`).
   - Publishes structured betslips containing match selections, odds, confidence ratings, and bookmaker booking codes.
   - Tracks personal analytics: slip win rates, won/lost tallies, and active slips.

3. **User (`user`)**:
   - Mobile app end-users consuming free and VIP tips.
   - Promo codes and bookmaker booking codes are kept protected behind VIP subscription / unlock passes.
   - Casts votes in community match prediction polls.

---

## Key Features

- **Tanzanian Shillings (`Tshs`) Currency**:
  - All financial transactions, subscriptions, stake amounts, and potential returns are formatted strictly in `Tshs`.
- **Auto-Scrolling Ads Board**:
  - Rotates banner cards automatically every **3 seconds** seamlessly without distracting UI indicators.
  - Promo codes remain protected until clicked or claimed by users.
- **Real Bookmaker Integrations**:
  - Dedicated configuration for Tanzania's top betting companies:
    - **Betika**
    - **SportyBet**
    - **1xBet**
    - **MozzartBet**
    - **Betway**
- **VIP Paywall Logic**:
  - VIP betslip promo codes and booking codes are obfuscated (`******`) on mobile API responses until the user unlocks them via active subscription or single slip pass.
- **Forest Green & Gold UI Theme**:
  - Blade super admin panel matches the mobile app design aesthetic with Forest Green (`#013220`), Gold (`#DDD06A`), and Neon Green (`#39FF88`).

---

## Mobile REST API Endpoints

All mobile endpoints are exposed under `/api/v1/`:

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/home` | Home screen feed: 3s ads rotator, featured big game, active poll, and top tipsters |
| `GET` | `/api/v1/betslips` | List active betslips (obfuscates VIP booking codes if unauthenticated/unsubscribed) |
| `POST` | `/api/v1/betslips/{id}/unlock` | Unlock single VIP betslip using Tshs pass |
| `POST` | `/api/v1/ads/{id}/click` | Track ad banner interaction and click counter |
| `POST` | `/api/v1/polls/{id}/vote` | Cast community prediction vote on a match poll |
| `POST` | `/api/v1/tipster/avatar` | Upload and update tipster portrait profile picture (multipart image) |
| `GET` | `/api/v1/status` | System health check and currency configuration |

---

## Quick Setup & Installation

### Requirements
- PHP 8.2 or higher (with `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`)
- MySQL 8.0+ or MariaDB 10.4+
- Composer

### Installation Steps

1. **Clone the repository**:
   ```bash
   git clone https://github.com/beastcodes27/betika-backend.git
   cd betika-backend
   ```

2. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update your database credentials in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=betwinner
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Launch Local Server**:
   ```bash
   php artisan serve
   ```

---

## Default Seeded Credentials

| Role | Email | Password | Access Portal |
|---|---|---|---|
| **Super Admin** | `admin@betwinner.co.tz` | `password` | `http://127.0.0.1:8000/admin` |
| **Top Tipster (Juma)** | `juma@betwinner.co.tz` | `password` | `http://127.0.0.1:8000/tipster/dashboard` |
| **Verified Tipster (Kassim)** | `kassim@betwinner.co.tz` | `password` | `http://127.0.0.1:8000/tipster/dashboard` |
| **End User** | `user@betwinner.co.tz` | `password` | Mobile App API (`/api/v1`) |

---

## Repository
- **Backend & Super Admin**: [https://github.com/beastcodes27/betika-backend.git](https://github.com/beastcodes27/betika-backend.git)
- **Frontend Mobile App**: [https://github.com/beastcodes27/betwinner.git](https://github.com/beastcodes27/betwinner.git)
