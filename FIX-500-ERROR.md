# 🔧 Fix HTTP 500 Error - Affinity Forum

## 🚨 Current Issue
Your website `affinity.wuaze.com` is showing an HTTP 500 error, which means there's a server-side problem.

## 🔍 Diagnostic Steps

### Step 1: Test Basic Functionality
1. **Visit the debug file**: `https://affinity.wuaze.com/debug-simple.php`
   - This will show you exactly what's working and what's broken
   - Look for red ❌ marks - those are the problems

2. **Test database connection**: `https://affinity.wuaze.com/test-db-connection.php`
   - This will verify your database is working properly
   - Check if all required tables exist

### Step 2: Common Causes & Fixes

#### A. Database Connection Issues
- **Problem**: Database server is down or credentials are wrong
- **Fix**: Check your hosting provider's database status
- **Verify**: Use `test-db-connection.php` to test connection

#### B. Missing Database Tables
- **Problem**: Required tables don't exist
- **Fix**: Run the database setup files:
  - `setup-basic-tables.php` - Creates basic structure
  - `setup-sample-data.php` - Adds sample data
  - `setup-admin-tables.php` - Creates admin tables

#### C. Server Configuration Issues
- **Problem**: .htaccess file has invalid rules
- **Fix**: Temporarily rename `.htaccess` to `.htaccess-backup`
- **Alternative**: Use the simplified `.htaccess-simple` file

#### D. PHP Version Compatibility
- **Problem**: Your hosting uses an older PHP version
- **Fix**: Check your hosting control panel for PHP version
- **Requirement**: PHP 7.4+ recommended

### Step 3: Quick Fixes to Try

#### Fix 1: Replace .htaccess
```bash
# Rename current .htaccess
mv .htaccess .htaccess-backup

# Use simplified version
cp .htaccess-simple .htaccess
```

#### Fix 2: Check File Permissions
```bash
# Set proper permissions
chmod 644 *.php
chmod 755 includes/
chmod 644 .htaccess
```

#### Fix 3: Test Minimal Version
- Try accessing `index-minimal.php` instead of `index.php`
- This will help isolate if the problem is in the main file

### Step 4: Database Setup (if needed)

If tables are missing, run these in order:

1. **Basic Tables**: `setup-basic-tables.php`
2. **Sample Data**: `setup-sample-data.php`  
3. **Admin Tables**: `setup-admin-tables.php`

### Step 5: Check Error Logs

Your hosting provider should have error logs. Common locations:
- **cPanel**: Error Logs section
- **DirectAdmin**: Error Logs
- **Custom hosting**: Check with your provider

## 🎯 What I've Fixed

I've already made these improvements to your `index.php`:

1. **Replaced problematic function calls** with direct database queries
2. **Added better error handling** for database operations
3. **Created diagnostic files** to identify issues
4. **Simplified .htaccess** for testing

## 🚀 Next Steps

1. **Test the debug files** to identify the exact problem
2. **Check database connection** using the test file
3. **Try the simplified .htaccess** if needed
4. **Run database setup** if tables are missing
5. **Contact your hosting provider** if the issue persists

## 📞 Need Help?

If you're still getting the 500 error after trying these fixes:

1. **Check the debug output** from the test files
2. **Look at your hosting error logs**
3. **Verify database credentials** in `config.php`
4. **Test with a minimal PHP file** to isolate the issue

## 🔒 Security Note

After fixing the issue, remember to:
- Remove or protect the debug files
- Restore the original .htaccess if you changed it
- Check that sensitive files are properly protected

---

**Good luck!** The debug files should give you the exact information needed to fix this issue.