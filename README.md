# 🚛 ELD - Electronic Logging Device System

## Overview

The ELD (Electronic Logging Device) System is a fleet management platform designed to help transportation companies monitor drivers, vehicles, and compliance with Hours of Service (HOS) regulations. The application provides real-time tracking, messaging, driver activity monitoring, panic alerts, document management, and administrative tools.

---

# Features

### Driver Management

-   Driver registration and profile management
-   Driver activity monitoring
-   Duty status management (Off Duty, Sleeper Berth, Driving, On Duty)
-   Driver document management
-   Driver logs and history

### Fleet Management

-   Vehicle management
-   Trailer management
-   Company management
-   Terminal management

### HOS Compliance

-   Electronic Log Book (ELD)
-   Hours of Service tracking
-   Violations monitoring
-   Cycle tracking
-   Daily log reports

### Real-Time Communication

-   Secure WebSocket messaging
-   One-to-one chat
-   Group chat
-   Read receipts
-   Live unread message count
-   Force logout support
-   Live duty status updates

### Panic Alert

-   Emergency panic alerts
-   Live notification to dispatch
-   Driver location information
-   Contact details

### Dashboard

-   Driver dashboard
-   Fleet dashboard
-   Live activity monitoring
-   Statistics and reports

### User Roles

-   Super Admin
-   Company Admin
-   Dispatcher
-   Driver

---

# Technology Stack

## Frontend

-   Next.js
-   React.js
-   Material UI (MUI)
-   Bootstrap Icons
-   Axios
-   NextAuth

## Backend

-   Laravel
-   PHP
-   REST APIs

## Real-Time Server

-   Node.js
-   WebSocket (ws)

## Database

-   MySQL

---

# System Architecture

```
Driver App / Web Portal
          │
          ▼
      Next.js Frontend
          │
 REST API │ WebSocket
          │
 ┌────────┴────────┐
 │                 │
 ▼                 ▼
Laravel API     Node.js WebSocket Server
 │                 │
 └────────┬────────┘
          ▼
       MySQL Database
```

---

# Major Modules

-   Authentication
-   Dashboard
-   Driver Management
-   Fleet Management
-   HOS Logs
-   Messaging System
-   Panic Alert
-   Notifications
-   Reports
-   Settings
-   User Management

---

# WebSocket Events

## Authentication

Client

```json
{
    "sendType": "auth",
    "token": "ACCESS_TOKEN"
}
```

Server

```json
{
    "sendType": "auth_success",
    "authenticated": true,
    "user_id": 101
}
```

---

## Send Message

```json
{
    "sendType": "message",
    "receiverId": 102,
    "message": "Hello"
}
```

---

## Update Read Status

```json
{
    "sendType": "update_read_status",
    "receiverId": 102
}
```

---

## Get Unread Messages

```json
{
    "sendType": "totalMsg"
}
```

---

## Change Duty Status

Server Event

```json
{
    "sendType": "change-duty-status",
    "status": "Driving"
}
```

---

## Force Logout

Server Event

```json
{
    "sendType": "force_logout"
}
```

---

# REST APIs

The application communicates with Laravel APIs for:

-   Login
-   Authentication
-   Driver Information
-   Fleet Data
-   Driver Logs
-   Upload Images
-   Upload Documents
-   Panic Alerts
-   Reports
-   User Management
-   Duty Status
-   Notifications

---

# Installation

## Clone Repository

```bash
git clone https://github.com/your-username/eld-project.git
```

---

## Frontend

```bash
cd frontend
npm install
npm run dev
```

---

## Backend

```bash
composer install
php artisan migrate
php artisan serve
```

---

## WebSocket Server

```bash
cd websocket
npm install
node server.js
```

---

# Environment Variables

Frontend

```env
NEXT_PUBLIC_API_URL=
NEXT_PUBLIC_WS_URL=
```

Backend

```env
APP_URL=
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

WebSocket

```env
PORT=3001
BACKEND_URL=
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

---

# Project Structure

```
Frontend (Next.js)
├── src/
├── components/
├── services/
├── app/

Backend (Laravel)
├── app/
├── routes/
├── database/
├── storage/

WebSocket
├── server.js
├── helpers/
├── controllers/
```

---

# Security

-   JWT Authentication
-   Protected APIs
-   WebSocket Token Authentication
-   Role-Based Access Control
-   Secure REST Communication
-   CORS Protection

---

# Future Enhancements

-   Push Notifications
-   GPS Tracking Improvements
-   Offline Log Synchronization
-   Driver Performance Analytics
-   Mobile Application Enhancements
-   Fleet Health Monitoring

---

# Contributors

**Prashant Chaubey**

Full Stack Developer

Technologies:

-   Laravel
-   Next.js
-   React
-   Node.js
-   WebSocket
-   MySQL
-   JavaScript

---

# License

This project is intended for internal/company use unless otherwise specified.
