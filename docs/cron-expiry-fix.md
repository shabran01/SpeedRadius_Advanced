# Cron Expiry Fix — Documentation
**Date:** March 15, 2026  
**File Fixed:** `system/cron.php`  
**Issue Type:** Bug — Hotspot customers expired on website but still active on MikroTik router

---

## The Problem

When a hotspot customer's package expired, the system was supposed to:
1. Remove the customer from the MikroTik router
2. Mark them as expired in the database

**But the code was doing it in the wrong order.**

The database was being marked as expired FIRST, before even attempting to remove the customer from the router. So when the router connection failed for any reason, the customer stayed active on MikroTik indefinitely — and the system never tried again because the database already said they were removed.

---

## Real World Scenario (Before Fix)

> **John** buys a 1-day hotspot package. It expires at midnight.

```
Midnight        — John's package expires
Midnight        — Cron runs
Midnight        — ✏️  Database updated: John = EXPIRED  ← happened FIRST
Midnight        — 💥  Router connection fails (brief hiccup, reboot, etc.)
Midnight        — John is NOT removed from MikroTik
Next cron run   — Looks for users WHERE status = 'on'
                — John is status = 'off' in DB → SKIPPED FOREVER
Result          — Website shows John as expired ✅
                  MikroTik still has John active ❌
                  John browses internet for free indefinitely 😱
```

---

## Why The Router Connection Could Fail

- Router rebooting at that exact moment
- Brief network hiccup between VPS and router
- RouterOS API port temporarily busy
- Router overloaded with traffic
- Wrong credentials or IP saved in settings

Any of these for even **1 second** during cron = customer stuck on router forever.

---

## Simple Explanation (The Security Guard Analogy)

Think of the cron job as a **security guard** whose job is to kick out expired customers.

### Old (Broken) Way
```
1. Guard writes in the logbook: "John removed" ← writes FIRST
2. Guard goes to the door to kick John out
3. Door is jammed (router offline) — John is NOT removed
4. Next shift: guard checks logbook, sees "John removed" → ignores John
5. John sits inside enjoying free internet forever
```

### New (Fixed) Way
```
1. Guard goes to the door and physically kicks John out FIRST
2. John is successfully removed
3. Guard NOW writes in the logbook: "John removed"
4. If door was jammed → writes NOTHING → tries again next shift
```

---

## The Fix Applied

**File:** `system/cron.php`

### Before (Broken Code)
```php
// Set status to off first to prevent repeated attempts if router connection fails
$u->status = 'off';
$u->save();   // ← Database updated BEFORE router removal

// Then try to remove from router...
(new $p['device'])->remove_customer($c, $p);
// If this fails, customer is stuck forever
```

### After (Fixed Code)
```php
// Remove from router FIRST
(new $p['device'])->remove_customer($c, $p);

// Only mark as expired AFTER successful router removal
$u->status = 'off';
$u->save();   // ← Database updated AFTER router removal
// If router removal fails, status stays 'on' → cron retries next run
```

---

## How The Fix Improves Things

| Situation | Old Code | New Code |
|---|---|---|
| Router offline at expiry | Website=Expired, Router=Still Active | Website=Still Active, Router=Still Active |
| Router comes back online | John stays free **forever** ❌ | John removed **next cron run** ✅ |
| Manual fix needed | Yes, every time ❌ | Never ✅ |
| Max extra free internet | Unlimited (days/weeks) 😱 | 5–10 minutes max 😌 |

---

## Your Setup Context

- **VPS hosted** — cron runs every 5 minutes
- **MikroTik** connected remotely via API over internet
- Since cron runs every 5 minutes, worst case a customer gets **5–10 extra minutes** if the router was briefly offline at the exact moment their package expired
- After that, cron retries automatically — **no manual intervention needed**

```
VPS (Your Server)          MikroTik (Your Router)
     │                            │
     │  ←── Internet/API ────────→│
     │                            │
  Cron runs here          Router at your location
  every 5 minutes
```

---

## What About Existing Stuck Customers?

If you already had customers stuck in this state (expired on website, still active on MikroTik) **before this fix**, the new code will NOT auto-heal them because their database status is already `off`.

To fix existing stuck customers:

**Option 1 — Manual MikroTik:**  
Go to MikroTik → IP → Hotspot → Users → delete the customer manually.

**Option 2 — Database Reset:**  
In your database, go to `tbl_user_recharges`, find the customer's record, change `status` from `off` back to `on`, then wait for the next cron run — it will properly remove them from the router and set `off` again.

---

## Summary

| | Detail |
|---|---|
| **Bug** | Database marked expired before router removal |
| **Effect** | Failed removals were never retried — customers stayed on router permanently |
| **Root Cause** | Wrong order of operations in `system/cron.php` |
| **Fix** | Move `$u->status = 'off'` to after successful router removal |
| **Self-Healing** | Yes — failed removals retry every 5 minutes automatically |
| **Worst Case Now** | Customer gets 5–10 extra minutes if router was briefly offline |
