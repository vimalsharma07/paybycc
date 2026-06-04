# Freelancer & Service Marketplace — Platform Blueprint

This document maps your requirements to the PayByCC codebase. **Phase 1** adds database foundation; UI flows roll out in phases without breaking current payments/KYC.

## User roles

| Role | Description |
|------|-------------|
| `customer` | Buyer — search freelancers, place orders, pay |
| `seller` | Freelancer / service provider — onboarding, services, settlements |
| `admin` | Platform operations |

Existing `users.is_admin` remains; `users.role` adds marketplace role (`customer` \| `seller`, default `customer`).

## Freelancer onboarding (seller)

Collect and store:

- Full name, mobile, email (existing)
- PAN, GST (optional), company name (optional), address
- Bank accounts (existing `banks` table)
- KYC (`users` + future `user_kyc` audit table)
- Services / sub-services (`freelancer_services` pivot)

## Service catalog

| Table | Purpose |
|-------|---------|
| `services` | Master categories |
| `sub_services` | Master sub-categories |
| `service_submissions` | Freelancer-proposed items pending admin approval |
| `freelancer_services` | Approved links: user ↔ sub_service |

## Customer features (planned)

Search by: mobile, PAN, service, sub-service, name/company.

## Orders (`marketplace_orders`)

Separate from card `payments` / ledger `transactions`.

**Amounts:** order_amount, platform_fee, gst, tds, tcs, net_settlement, currency.

**Statuses:** order, payment, settlement, safe (risk).

**Safe status** gates settlement: `pending_review` → `safe` \| `hold` \| `rejected`.

## Settlement engine (planned)

Automated settlements after: seller KYC active, safe status `safe`, payment `paid`. TDS/TCS/GST recorded in compliance tables.

## Admin (existing + planned)

| Area | Status |
|------|--------|
| Users, banks, transactions, logs, gateways | Live |
| Services / sub-services CRUD | Phase 2 |
| Service approval queue | Phase 2 |
| KYC approve/reject | Phase 3 |
| Orders & settlements | Phase 4 |
| TDS/TCS/GST reports | Phase 5 |

## Application logs

Use **Admin → Logs**, filter by channel:

| Channel | Flow |
|---------|------|
| `auth` | Register, login, logout, OTP |
| `kyc` | KYC form & submit |
| `bank` | Add / update / remove bank |
| `otp` / `sms` | OTP delivery |
| `payment` | (wire when needed) |

## Implementation phases

1. **Foundation** — migrations, enums, `config/platform.php` (this release)
2. **Seller onboarding UI** — role selection, profile, service picker, submissions
3. **Customer search & orders** — search index, order creation, payment link
4. **Settlement & compliance** — settlement job, TDS/TCS/GST tables, reports
5. **Admin expansion** — approvals, risk review, overrides

## SQL

Run migrations:

```bash
php artisan migrate
```

See `database/migrations/2026_05_24_*_marketplace_foundation.php`.
