# Inventory Prediction System - Implementation Guide

## Overview

The Inventory Prediction System is a comprehensive machine learning solution for optimizing inventory management. It provides:

- **Demand Forecasting**: Predict future demand for items using historical sales patterns
- **Stock-out Risk Analysis**: Identify items at risk of running out of stock
- **Anomaly Detection**: Detect unusual sales patterns and inventory discrepancies
- **Seasonality Detection**: Identify seasonal trends in demand by course and category
- **Return/Churn Risk**: Analyze return patterns and product quality risks

---

## System Architecture

### Components

```
┌─────────────────────────────────────────────────────────────┐
│                    Admin Dashboard                          │
│              (admin/predictions.php)                        │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────┴──────────────────────────────────────┐
│                  API Layer                                  │
│              (api/predictions.php)                          │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────┴──────────────────────────────────────┐
│            PHP Wrapper (PredictionEngine)                   │
│         (app/classes/PredictionEngine.class.php)           │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────┴──────────────────────────────────────┐
│         Python Prediction Engine                            │
│         (app/prediction_engine.py)                          │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────┴──────────────────────────────────────┐
│              Database Layer                                 │
│          (prediction_schema.sql)                            │
└─────────────────────────────────────────────────────────────┘
```

### Technology Stack

- **Backend**: PHP 8.2+
- **ML/Data Processing**: Python 3.7+
- **Database**: MySQL 10.4.32+
- **Frontend**: Bootstrap 4, jQuery, AdminLTE 3
- **ML Libraries**: scikit-learn, pandas, numpy

---

## Installation & Setup

### Step 1: Install Python Dependencies

```bash
pip install pandas numpy scikit-learn statsmodels scipy
```

**Required packages:**
- pandas >= 1.3.0
- numpy >= 1.21.0
- scikit-learn >= 1.0.0
- scipy >= 1.7.0

### Step 2: Create Database Tables

```bash
mysql -u root -p inventory < database/prediction_schema.sql
```

Or manually run the SQL from `database/prediction_schema.sql` in phpMyAdmin.

### Step 3: Create Required Directories

```bash
mkdir -p app/logs
chmod 755 app/logs
```

### Step 4: Verify Python Path

Edit `app/classes/PredictionEngine.class.php` and ensure the Python path is correct:

```php
private $python_path = 'python';  // or 'python3' depending on your system
```

### Step 5: Set Up Cron Jobs (Optional)

See `app/scripts/CRON_SETUP.txt` for scheduling options.

---

## Usage

### Via Admin Dashboard

1. Navigate to **Admin Panel → Predictions**
2. View **Stock-out Risk Summary** with current risk levels
3. Click **"Run All Predictions"** to generate forecasts
4. View results in tabs:
   - **Stock-out Risk**: Shows items at risk
   - **Demand Forecast**: Shows predicted quantities
   - **Execution Logs**: Shows past runs

### Via API

**Get Stock-out Risk Summary:**
```bash
GET /api/predictions/stockout-risks?risk_level=CRITICAL
```

**Get Recent Forecasts:**
```bash
GET /api/predictions/forecasts?item_id=23&days=7
```

**Generate Predictions for Specific Item:**
```bash
POST /api/predictions/generate
Content-Type: application/json

{
    "item_id": 23
}
```

**Run All Predictions:**
```bash
POST /api/predictions/generate-all
Content-Type: application/json

{
    "limit": 100
}
```

**Get System Summary:**
```bash
GET /api/predictions/summary
```

### Via Command Line

**Test the engine:**
```bash
php app/scripts/prediction_scheduler.php daily
```

**Output:**
```
[2026-02-02 14:30:00] Starting daily prediction cycle...
Description: Daily predictions for top 200 items

✓ Predictions completed successfully
  Items processed: 187
  Errors: 0
  Execution time: 45.32s
```

---

## Database Schema

### Core Tables

#### 1. `inventory_demand_forecast`
Stores demand predictions for items with confidence scores.

**Key Columns:**
- `item_id`: Reference to inventory item
- `forecast_date`: Date of forecast
- `predicted_quantity`: Forecasted demand
- `confidence_score`: 0-1, higher is better
- `model_type`: Type of model used (LinearRegression, LSTM, Prophet)

#### 2. `inventory_stockout_risk`
Tracks stock-out risk analysis for items.

**Key Columns:**
- `item_id`: Reference to inventory item
- `risk_level`: CRITICAL, HIGH, MEDIUM, LOW
- `days_until_stockout`: Estimated days until stockout
- `reorder_recommended`: Boolean flag
- `recommended_quantity`: Suggested reorder amount

#### 3. `inventory_seasonality`
Stores seasonal patterns by item/course/month.

**Key Columns:**
- `item_id`: Reference to item
- `course_id`: Reference to course
- `month`: Month (1-12)
- `season_type`: PEAK, NORMAL, LOW
- `average_sales`: Historical average

#### 4. `inventory_anomalies`
Detected anomalies in sales and inventory.

**Key Columns:**
- `anomaly_type`: Type of anomaly
- `anomaly_score`: 0-100, higher = more anomalous
- `severity`: LOW, MEDIUM, HIGH, CRITICAL
- `is_reviewed`: Whether admin has reviewed

#### 5. `inventory_model_metrics`
Performance metrics for prediction models.

**Key Columns:**
- `model_name`: Name of the model
- `accuracy_score`: Model accuracy
- `mae`: Mean Absolute Error
- `rmse`: Root Mean Squared Error
- `f1_score`: F1 score for classification models

#### 6. `inventory_recommendations`
Actionable recommendations based on predictions.

**Key Columns:**
- `item_id`: Reference to item
- `recommendation_type`: REORDER, REDUCE_STOCK, etc.
- `urgency`: LOW, MEDIUM, HIGH, CRITICAL
- `is_implemented`: Implementation status

#### 7. `inventory_prediction_log`
Execution logs for prediction runs.

**Key Columns:**
- `execution_type`: Type of prediction run
- `status`: PENDING, RUNNING, SUCCESS, FAILED
- `items_processed`: Number of items
- `execution_time_seconds`: Runtime

---

## Prediction Models

### 1. Demand Forecast Model

**Type:** Linear Regression with Time Series Features

**Features Used:**
- 30-day rolling average
- Sales volatility (standard deviation)
- Peak and minimum sales
- Previous day's sales
- 7-day moving average
- Day of week
- Month indicator

**Output:**
- 7-day demand forecast
- Confidence scores (0-1)
- Forecast intervals (lower/upper bounds)

**Performance Metrics:**
- MAE (Mean Absolute Error)
- RMSE (Root Mean Squared Error)
- MAPE (Mean Absolute Percentage Error)

### 2. Stockout Risk Model

**Type:** Regression-based Risk Scoring

**Inputs:**
- Current inventory quantity
- Daily sales rate
- Lead time (default: 7 days)

**Risk Calculation:**
```
days_until_stockout = current_quantity / daily_sales_rate

Risk Levels:
- CRITICAL: days_until_stockout < lead_time
- HIGH: lead_time ≤ days < lead_time * 2
- MEDIUM: lead_time * 2 ≤ days < lead_time * 3
- LOW: days ≥ lead_time * 3
```

**Reorder Recommendation:**
- Recommended quantity = daily_sales_rate × 30 × 1.2 (20% safety stock)

### 3. Anomaly Detection Model

**Type:** Isolation Forest

**Detects:**
- Unusual spikes in sales
- Drops in demand
- Inventory discrepancies
- Suspicious transactions

**Output:**
- Anomaly score (0-100)
- List of detected anomalies
- Percentage of anomalous data points

### 4. Seasonality Detection

**Type:** Statistical Analysis

**Detects:**
- Monthly patterns
- Course-specific trends
- Peak and low seasons

**Output:**
- Seasonal indices per month
- Season classification (PEAK, NORMAL, LOW)
- Expected sales by season

---

## Configuration

### Python Engine Configuration

Edit `app/prediction_engine.py`:

```python
# Lookback window for features (days)
lookback = 30

# Forecast horizon (days)
forecast_days = 7

# Anomaly detection contamination rate
contamination = 0.1

# Safety stock multiplier for reorder
safety_stock = 1.2
```

### PHP Wrapper Configuration

Edit `app/classes/PredictionEngine.class.php`:

```php
// Python executable path
private $python_path = 'python';

// Default lead time for stock-out analysis (days)
$lead_time_days = 7;

// Number of historical days to analyze
$analysis_days = 90;

// Monthly analysis period
$monthly_period = 12;
```

---

## API Reference

### Endpoints

#### GET /api/predictions/forecasts

Get recent demand forecasts.

**Parameters:**
- `item_id` (optional): Filter by specific item
- `days` (optional): Number of days back (default: 7)

**Response:**
```json
{
  "success": true,
  "count": 42,
  "forecasts": [
    {
      "id": 1,
      "item_id": 23,
      "item_code": "AMT-001",
      "item_name": "AMT (M) Uniform",
      "forecast_date": "2026-02-03",
      "predicted_quantity": 5,
      "confidence_score": 0.85,
      "model_type": "LinearRegression",
      "created_at": "2026-02-02 14:30:00"
    }
  ]
}
```

#### GET /api/predictions/stockout-risks

Get stock-out risk summary.

**Parameters:**
- `risk_level` (optional): CRITICAL, HIGH, MEDIUM, LOW

**Response:**
```json
{
  "success": true,
  "summary": {
    "CRITICAL": 2,
    "HIGH": 5,
    "MEDIUM": 8,
    "LOW": 45
  },
  "items": [
    {
      "id": 1,
      "item_id": 23,
      "item_code": "AMT-001",
      "item_name": "AMT (M) Uniform",
      "current_quantity": 109,
      "daily_sales_rate": 2.5,
      "days_until_stockout": 44,
      "risk_level": "LOW",
      "reorder_recommended": false,
      "recommended_quantity": 90,
      "analyzed_at": "2026-02-02 14:30:00"
    }
  ]
}
```

#### POST /api/predictions/generate

Generate predictions for a specific item.

**Request Body:**
```json
{
  "item_id": 23
}
```

**Response:**
```json
{
  "success": true,
  "item_id": 23,
  "demand_forecast": {
    "predictions": [5, 6, 4, 7, 5, 6, 5],
    "confidence_scores": [0.85, 0.82, 0.79, 0.76, 0.73, 0.70, 0.67]
  },
  "stockout_risk": {
    "risk_level": "LOW",
    "days_until_stockout": 44,
    "risk_score": 20,
    "reorder_recommended": false,
    "recommended_quantity": 90
  },
  "timestamp": "2026-02-02 14:30:00"
}
```

#### POST /api/predictions/generate-all

Run predictions for all items.

**Request Body:**
```json
{
  "limit": 100
}
```

**Response:**
```json
{
  "success": true,
  "items_processed": 87,
  "errors": 0,
  "execution_time": 45.32,
  "timestamp": "2026-02-02 14:30:00"
}
```

#### GET /api/predictions/summary

Get system summary and model statistics.

**Response:**
```json
{
  "success": true,
  "summary": {
    "total_items_predicted": 52,
    "total_forecasts": 2847,
    "last_forecast_generated": "2026-02-02 14:30:00",
    "active_models": 4
  },
  "models": [
    {
      "id": 1,
      "model_name": "DemandForecast",
      "model_type": "LinearRegression",
      "accuracy_score": 0.867,
      "mae": 1.23,
      "rmse": 2.45,
      "last_trained_at": "2026-02-02 10:00:00"
    }
  ],
  "timestamp": "2026-02-02 14:30:00"
}
```

#### GET /api/predictions/execution-logs

Get execution logs.

**Parameters:**
- `limit` (optional): Number of logs to return (default: 50)

**Response:**
```json
{
  "success": true,
  "count": 10,
  "logs": [
    {
      "id": 1,
      "execution_type": "COMPREHENSIVE",
      "status": "SUCCESS",
      "items_processed": 87,
      "predictions_generated": 609,
      "errors_encountered": 0,
      "execution_time_seconds": 45.32,
      "started_at": "2026-02-02 14:30:00",
      "completed_at": "2026-02-02 14:31:00"
    }
  ]
}
```

---

## Troubleshooting

### Issue: Python Engine Not Found

**Error:**
```
Failed to execute Python engine
```

**Solution:**
1. Verify Python is installed: `python --version`
2. Update Python path in `PredictionEngine.class.php`
3. Check file permissions on `prediction_engine.py`

### Issue: Insufficient Data

**Error:**
```
Insufficient data for training. Need at least 10 data points.
```

**Solution:**
- The system needs historical sales data to make predictions
- Run the system after items have accumulated sales history
- Minimum 10 historical data points required per item

### Issue: Database Connection Error

**Error:**
```
Prepare failed: ...
```

**Solution:**
1. Verify database connection settings in `Database.class.php`
2. Ensure prediction tables exist: Run `prediction_schema.sql`
3. Check database user permissions

### Issue: Cron Job Not Running

**Solution:**
1. Verify cron syntax: `crontab -l`
2. Check logs: `tail -f app/logs/cron_daily.log`
3. Test manually: `php app/scripts/prediction_scheduler.php daily`
4. Verify PHP path: `which php`

---

## Performance Optimization

### Tips for Better Predictions

1. **Accumulate Data**: Run system after at least 1-2 months of sales history
2. **Regular Updates**: Schedule daily or hourly predictions
3. **Tune Lead Times**: Adjust `lead_time_days` based on actual supplier performance
4. **Monitor Accuracy**: Check model metrics in `inventory_model_metrics` table
5. **Handle Seasonality**: The system learns seasonal patterns automatically

### Database Optimization

```sql
-- Add these indexes for faster queries
CREATE INDEX idx_forecast_item_date ON inventory_demand_forecast(item_id, forecast_date);
CREATE INDEX idx_risk_item_level ON inventory_stockout_risk(item_id, risk_level);
CREATE INDEX idx_anomaly_type_date ON inventory_anomalies(anomaly_type, created_at);

-- Clean up old data (keep last 90 days)
DELETE FROM inventory_demand_forecast WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

---

## Integration Examples

### Example 1: Display Stockout Alerts in Item Details

```php
<?php
require 'app/classes/PredictionEngine.class.php';
$engine = new PredictionEngine();
$risk = $engine->analyzeStockoutRisk($item_id);

if ($risk['success'] && $risk['risk_analysis']['reorder_recommended']) {
    echo '<div class="alert alert-warning">
        Stock-out risk: ' . $risk['risk_analysis']['risk_level'] . '
        Days until stockout: ' . $risk['risk_analysis']['days_until_stockout'] . '
        Recommended order: ' . $risk['risk_analysis']['recommended_quantity'] . ' units
    </div>';
}
?>
```

### Example 2: Automated Reorder Trigger

```php
<?php
// In a scheduled job
$engine = new PredictionEngine();
$risk = $engine->getStockoutRiskSummary('CRITICAL');

foreach ($risk['items'] as $item) {
    if ($item['reorder_recommended']) {
        // Create automatic purchase order
        createPurchaseOrder($item['item_id'], $item['recommended_quantity']);
        // Send alert email
        sendAlert("Item {$item['item_name']} needs reorder");
    }
}
?>
```

### Example 3: Forecast-Based Pricing

```php
<?php
$engine = new PredictionEngine();
$forecast = $engine->generateDemandForecast($item_id);

$avg_demand = array_sum($forecast['forecast']['predictions']) / 
              count($forecast['forecast']['predictions']);

// Adjust pricing based on demand
if ($avg_demand > 10) {
    $adjusted_price = $base_price * 1.1;  // 10% premium
} else if ($avg_demand < 2) {
    $adjusted_price = $base_price * 0.9;  // 10% discount
}
?>
```

---

## Support & Maintenance

### Regular Maintenance Tasks

1. **Weekly**: Review prediction accuracy and anomalies
2. **Monthly**: Retrain models with new data
3. **Quarterly**: Review and optimize lead times
4. **Annually**: Audit all prediction logic and parameters

### Monitoring

Check execution logs regularly:
```bash
tail -f app/logs/prediction_engine.log
tail -f app/logs/cron_daily.log
```

Query model performance:
```sql
SELECT * FROM inventory_model_metrics ORDER BY last_trained_at DESC;
SELECT * FROM inventory_prediction_log WHERE status = 'FAILED';
```

---

## Version History

- **v1.0** (Feb 2, 2026): Initial release
  - Demand forecasting
  - Stock-out risk analysis
  - Anomaly detection
  - Admin dashboard
  - API endpoints
  - Cron scheduler

---

## License & Attribution

This system is part of the Inventory Management System v2.
For questions or improvements, contact the development team.
