# Inventory Prediction System - Quick Start Guide

## What's New?

Your inventory system now includes AI-powered prediction models for:
- **Demand Forecasting**: Predict what items customers will order
- **Stock-out Risk**: Know which items might run out of stock soon
- **Anomaly Detection**: Spot unusual sales patterns
- **Seasonality Trends**: Understand seasonal demand patterns

## Quick Setup (5 minutes)

### 1. Create Database Tables

```bash
# Using command line:
mysql -u root -p inventory < database/prediction_schema.sql

# Or via phpMyAdmin:
# - Go to SQL tab
# - Copy contents of database/prediction_schema.sql
# - Execute
```

### 2. Install Python (If Needed)

Download from https://www.python.org/downloads/

Then install ML libraries:
```bash
pip install pandas numpy scikit-learn scipy
```

### 3. Verify Installation

```bash
php install_predictions.php
```

Should show all green checkmarks ✓

## Using Predictions

### Access Dashboard

1. Login to Admin Panel
2. Click **Predictions** in sidebar
3. View risk summary and forecasts
4. Click **"Run All Predictions"** to generate new ones

### Understand Risk Levels

| Level | Meaning | Action |
|-------|---------|--------|
| 🔴 CRITICAL | Will run out in <7 days | Reorder immediately |
| 🟠 HIGH | Will run out in 7-14 days | Plan reorder soon |
| 🟡 MEDIUM | Will run out in 14-21 days | Monitor |
| 🟢 LOW | Will run out in >21 days | No action needed |

### Check Forecasts

Look at **"Demand Forecast"** tab to see predicted demand for next 7 days.

## API Usage

### Get Stock-out Alerts

```bash
curl "http://localhost/inventory_v2/api/predictions/stockout-risks?risk_level=CRITICAL"
```

### Generate Single Item Prediction

```bash
curl -X POST http://localhost/inventory_v2/api/predictions/generate \
  -H "Content-Type: application/json" \
  -d '{"item_id": 23}'
```

### Run All Predictions

```bash
curl -X POST http://localhost/inventory_v2/api/predictions/generate-all
```

## Manual Testing

### Test Prediction Engine

```bash
php app/scripts/prediction_scheduler.php daily
```

Expected output:
```
[2026-02-02 14:30:00] Starting daily prediction cycle...
✓ Predictions completed successfully
  Items processed: 187
  Errors: 0
  Execution time: 45.32s
```

## Schedule Automatic Predictions

### For Linux/Mac (Cron)

Edit crontab:
```bash
crontab -e
```

Add these lines:
```cron
# Hourly predictions
0 * * * * cd /path/to/inventory_v2/app/scripts && php prediction_scheduler.php hourly >> ../logs/cron.log 2>&1

# Daily predictions at 2 AM
0 2 * * * cd /path/to/inventory_v2/app/scripts && php prediction_scheduler.php daily >> ../logs/cron.log 2>&1
```

### For Windows (Task Scheduler)

1. Open Task Scheduler
2. Create Basic Task
3. Set trigger (hourly/daily)
4. Action: Start program
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `C:\xampp\htdocs\inventory_v2\app\scripts\prediction_scheduler.php daily`

## Troubleshooting

### "Python not found"
- Install Python from https://www.python.org/
- Or update Python path in `app/classes/PredictionEngine.class.php`

### "Insufficient data for training"
- System needs historical sales data
- Run after 1-2 months of transactions
- Minimum 10 data points per item

### "Connection failed"
- Check database connection in `app/classes/Database.class.php`
- Verify prediction tables exist: Run SQL schema file

### Check Logs
```bash
# View prediction logs
tail -f app/logs/prediction_engine.log

# View cron logs
tail -f app/logs/cron_daily.log
```

## Features Explained

### Demand Forecasting
Uses historical sales data to predict future demand. Shows next 7 days with confidence scores.

**Example Output:**
```
Day 1: 5 units (85% confidence)
Day 2: 6 units (82% confidence)
Day 3: 4 units (79% confidence)
```

### Stock-out Risk
Analyzes current inventory and sales velocity to predict when items will run out.

**Calculation:**
```
Days until stockout = Current Inventory ÷ Daily Sales Rate
Example: 100 units ÷ 2.5 units/day = 40 days
```

### Recommended Actions
System suggests optimal reorder quantities based on:
- Average daily sales
- Lead time for ordering
- Safety stock buffer (20%)

**Example:**
```
Current Stock: 50 units
Daily Sales: 2.5 units
Recommended Reorder: 90 units (covers 30 days)
```

## Database Tables Overview

| Table | Purpose |
|-------|---------|
| inventory_demand_forecast | Stores demand predictions |
| inventory_stockout_risk | Tracks stock-out risk analysis |
| inventory_seasonality | Stores seasonal patterns |
| inventory_anomalies | Detected unusual patterns |
| inventory_recommendations | Actionable recommendations |
| inventory_prediction_log | Execution logs |
| inventory_model_metrics | Model performance metrics |

## Performance Tips

1. **Run predictions regularly**: Daily is ideal, hourly for high-volume items
2. **Use API for integration**: Easier than dashboard for automated workflows
3. **Monitor accuracy**: Check model metrics in database
4. **Adjust parameters**: Fine-tune lead times based on your supplier
5. **Archive old data**: Keep database lean and fast

## Full Documentation

See **PREDICTION_SYSTEM_GUIDE.md** for:
- Detailed architecture
- Complete API reference
- Configuration options
- Integration examples
- Advanced troubleshooting

## Support

For issues:
1. Check `app/logs/prediction_engine.log`
2. Review PREDICTION_SYSTEM_GUIDE.md
3. Test manually: `php app/scripts/prediction_scheduler.php daily`
4. Verify database tables: `mysql -u root -p inventory -e "SHOW TABLES LIKE 'inventory_prediction%';"`

---

**System Version**: 1.0  
**Last Updated**: February 2, 2026
