# Inventory Prediction System - Setup Checklist

## ✅ Pre-Installation Requirements

- [ ] PHP 8.0 or higher
- [ ] MySQL 10.4 or higher  
- [ ] Read/write access to `app/logs` directory
- [ ] Access to command line/terminal

## ✅ Installation Steps

### Phase 1: Database Setup
- [ ] Backup current database
- [ ] Run: `mysql -u root -p inventory < database/prediction_schema.sql`
- [ ] Verify tables created: `mysql -u root -p inventory -e "SHOW TABLES LIKE 'inventory_prediction%';"`

### Phase 2: Python Setup (Optional but Recommended)
- [ ] Install Python 3.7+ from https://www.python.org/downloads/
- [ ] Verify: `python --version`
- [ ] Install libraries: `pip install pandas numpy scikit-learn scipy`
- [ ] Verify: `pip show pandas`

### Phase 3: System Verification
- [ ] Create directory: `mkdir -p app/logs && chmod 755 app/logs`
- [ ] Run: `php install_predictions.php`
- [ ] All checks should show ✓

### Phase 4: Test Predictions
- [ ] Run: `php app/scripts/prediction_scheduler.php daily`
- [ ] Expected output: "Predictions completed successfully"
- [ ] Check database: `SELECT COUNT(*) FROM inventory_demand_forecast;`

## ✅ Post-Installation Configuration

### Dashboard Access
- [ ] Login to admin panel
- [ ] Navigate to **Predictions** in sidebar
- [ ] Verify dashboard loads
- [ ] Try "Run All Predictions" button

### API Testing
- [ ] Test endpoint: `curl http://localhost/api/predictions/summary`
- [ ] Expected: JSON response with success: true

### Scheduling (Optional)
- [ ] Review: `app/scripts/CRON_SETUP.txt`
- [ ] For Linux/Mac: Add to crontab
- [ ] For Windows: Create scheduled task
- [ ] Verify runs (check logs after first run)

## ✅ Verification Checklist

### Database
- [ ] 9 new tables exist in `inventory` database
- [ ] Tables have proper indexes
- [ ] No errors in table structure

### Files
- [ ] `app/prediction_engine.py` exists (612 lines)
- [ ] `app/classes/PredictionEngine.class.php` exists (578 lines)
- [ ] `admin/predictions.php` exists (468 lines)
- [ ] `api/predictions.php` exists (297 lines)
- [ ] All files are readable

### Permissions
- [ ] `app/logs/` directory is writable
- [ ] `app/scripts/` directory exists
- [ ] Python script is executable

### Documentation
- [ ] `QUICK_START.md` readable
- [ ] `PREDICTION_SYSTEM_GUIDE.md` readable
- [ ] `PREDICTIONS_README.txt` visible
- [ ] `IMPLEMENTATION_SUMMARY.md` available

## ✅ First Run Verification

- [ ] Dashboard loads without errors
- [ ] Risk summary shows data
- [ ] Tables display current data
- [ ] "Run Predictions" button works
- [ ] New log entries appear in database

## ✅ Troubleshooting Verification

- [ ] Can view `app/logs/prediction_engine.log`
- [ ] Can view `app/logs/cron_daily.log` (if scheduled)
- [ ] Database queries work manually
- [ ] Python engine runs manually
- [ ] API endpoint responds with JSON

## ✅ Integration Testing

- [ ] PHP class loads: `require 'app/classes/PredictionEngine.class.php'`
- [ ] Can instantiate: `$engine = new PredictionEngine($db);`
- [ ] Can call methods without errors
- [ ] Results stored in database

## ✅ Performance Baseline

- [ ] First run takes < 2 minutes for 52 items
- [ ] Dashboard loads in < 1 second
- [ ] API responds in < 500ms
- [ ] No database locks or timeouts

## ✅ Security Review

- [ ] No SQL injection vulnerabilities
- [ ] Proper error handling (no exposing paths)
- [ ] File permissions are secure
- [ ] Logs don't contain sensitive data
- [ ] Only admins can access dashboard

## ✅ Documentation Review

- [ ] QUICK_START.md is clear and accurate
- [ ] PREDICTION_SYSTEM_GUIDE.md covers all features
- [ ] API endpoints documented
- [ ] Configuration options explained
- [ ] Troubleshooting section helpful

## 📋 Sign-Off

- [ ] System is working as expected
- [ ] All documentation is in place
- [ ] Team members trained on usage
- [ ] Backup procedures documented
- [ ] Ready for production use

---

## 🆘 If Something Goes Wrong

### Common Issues & Solutions

**Issue**: Python not found  
**Solution**: 
- Ensure Python 3.7+ is installed
- Check `python --version`
- Update Python path in `PredictionEngine.class.php`

**Issue**: Insufficient data for training  
**Solution**:
- Need at least 10 historical data points
- Wait 1-2 months for sales history
- Test with demo data first

**Issue**: Database tables don't exist  
**Solution**:
- Run: `mysql -u root -p inventory < database/prediction_schema.sql`
- Verify: `SHOW TABLES LIKE 'inventory_prediction%';`

**Issue**: "Connection failed"  
**Solution**:
- Check `app/classes/Database.class.php` settings
- Verify MySQL is running
- Check username/password

**Issue**: Permission denied errors  
**Solution**:
- `chmod 755 app/logs`
- `chmod 644 app/prediction_engine.py`
- Check directory ownership

**For more help**, see:
- `QUICK_START.md` - Quick reference
- `PREDICTION_SYSTEM_GUIDE.md` - Detailed troubleshooting
- `app/logs/prediction_engine.log` - Error logs

---

## 📞 Support Resources

| Resource | Location | Purpose |
|----------|----------|---------|
| Quick Guide | QUICK_START.md | 5-min setup |
| Full Guide | PREDICTION_SYSTEM_GUIDE.md | Complete reference |
| Summary | IMPLEMENTATION_SUMMARY.md | What was built |
| Scheduling | app/scripts/CRON_SETUP.txt | Automation setup |
| Logs | app/logs/ | Troubleshooting |
| API | api/predictions.php | Integration |
| Dashboard | admin/predictions.php | Admin UI |

---

## 🎯 Success Indicators

✅ All items below should be true:

- Dashboard loads without errors
- Predictions generate in < 2 minutes
- Database contains forecast data
- API responds with JSON
- Logs show successful execution
- Risk summary shows accurate data
- No JavaScript errors in console
- All tables have data

---

**Checklist Version**: 1.0  
**Last Updated**: February 2, 2026  
**Status**: Ready for Implementation ✓
