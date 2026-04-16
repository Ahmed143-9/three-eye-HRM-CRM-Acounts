# HRM Asset Management Module

## 📋 Overview
Complete Asset Management system for HRM application with employee asset tracking, assignment history, and asset request workflow.

---

## ✨ Features Implemented

### 1. **Company Assets Management**
- ✅ Asset CRUD operations (Create, Read, Update, Delete)
- ✅ Unique asset code auto-generation (e.g., AST00001)
- ✅ Asset categories: IT, Furniture, Electronics, Vehicles, Machinery, Other
- ✅ Condition tracking: New, Used, Damaged, Under Maintenance
- ✅ Status management: Available, Assigned, Lost, Maintenance
- ✅ Asset image upload support
- ✅ Additional fields: Manufacturer, Model Number, Serial Number, Location, Warranty

### 2. **Employee Asset Assignment**
- ✅ Assign available assets to employees
- ✅ Return assets from employees
- ✅ Assignment document upload
- ✅ Remarks/notes for each assignment
- ✅ Prevent duplicate assignments
- ✅ Automatic status updates

### 3. **Asset History Tracking**
- ✅ Complete assignment history per asset
- ✅ Track assign date, return date, status changes
- ✅ View who assigned the asset
- ✅ Document history

### 4. **Employee Asset View**
- ✅ View all current assets assigned to an employee
- ✅ View past asset assignments
- ✅ Employee asset summary

### 5. **Asset Request System (Bonus)**
- ✅ Employees can request assets
- ✅ HR approval/rejection workflow
- ✅ Rejection reason tracking
- ✅ Request status management

---

## 🗄️ Database Structure

### Tables Created/Modified:

1. **assets** (Enhanced)
   - asset_code (unique, auto-generated)
   - name, category, condition, status
   - purchase_date, supported_date, amount
   - manufacturer, model_number, serial_number
   - location, warranty_until, image
   - description, created_by

2. **employee_assets** (New)
   - employee_id (FK → employees)
   - asset_id (FK → assets)
   - assign_date, return_date
   - status (Assigned, Returned, Lost, Damaged)
   - remarks, document
   - assigned_by, created_by

3. **asset_requests** (New)
   - employee_id (FK → employees)
   - asset_id (FK → assets)
   - status (Pending, Approved, Rejected)
   - reason, rejection_reason
   - approved_by, requested_date, approved_date

---

## 📁 File Structure

```
app/
├── Models/
│   ├── Asset.php (Enhanced)
│   ├── EmployeeAsset.php (New)
│   ├── AssetRequest.php (New)
│   └── Employee.php (Enhanced with relationships)
│
├── Http/Controllers/
│   └── AssetController.php (Enhanced)
│
database/
└── migrations/
    ├── 2026_04_15_000001_add_enhanced_fields_to_assets_table.php
    ├── 2026_04_15_000002_create_employee_assets_table.php
    └── 2026_04_15_000003_create_asset_requests_table.php
│
resources/
└── views/
    └── assets/
        ├── index.blade.php (Dashboard with statistics)
        ├── create.blade.php (Create asset form)
        ├── edit.blade.php (Edit asset form - reuse create)
        ├── show.blade.php (Asset details view)
        ├── assign.blade.php (Assign asset to employee)
        ├── return.blade.php (Return asset from employee)
        ├── history.blade.php (Asset assignment history)
        ├── employee-assets.blade.php (Employee asset view)
        └── requests.blade.php (Asset requests management)
│
public/
└── uploads/
    ├── assets/ (Asset images)
    └── asset_documents/ (Assignment documents)
```

---

## 🚀 Routes

### Main Routes:
```php
GET     /account-assets                      → Index (Dashboard)
GET     /account-assets/create               → Create form
POST    /account-assets                      → Store asset
GET     /account-assets/{id}                 → Show asset details
GET     /account-assets/{id}/edit            → Edit form
POST    /account-assets/{id}                 → Update asset
DELETE  /account-assets/{id}                 → Delete asset
```

### Assignment Routes:
```php
GET     /account-assets/{id}/assign          → Assign form
POST    /account-assets/{id}/assign          → Process assignment
GET     /account-assets/{id}/return          → Return form
POST    /account-assets/{id}/return          → Process return
```

### History & Reports:
```php
GET     /account-assets/{id}/history         → Asset history
GET     /account-assets/employee/{id}        → Employee assets
```

### Request Management:
```php
GET     /account-assets/requests             → All requests
POST    /account-assets/requests/{id}/approve → Approve request
POST    /account-assets/requests/{id}/reject  → Reject request
```

---

## 🔐 Permissions Required

Add these permissions to your roles/permissions system:
- `manage assets` - View all assets and history
- `create assets` - Create new assets
- `edit assets` - Edit existing assets
- `delete assets` - Delete assets
- `assign assets` - Assign assets to employees
- `return assets` - Process asset returns

---

## 💡 Usage Examples

### Creating an Asset:
1. Navigate to Assets → Add Asset
2. Fill in required fields: Name, Category, Condition, Purchase Date, Amount
3. Optional: Add manufacturer details, serial number, image
4. Click "Create Asset"
5. Asset code is auto-generated (e.g., AST00001)

### Assigning an Asset:
1. From asset list, click the "Assign" button (green) on available assets
2. Select employee from dropdown
3. Set assignment date
4. Add remarks (optional)
5. Upload assignment document (optional)
6. Click "Assign Asset"
7. Asset status automatically changes to "Assigned"

### Returning an Asset:
1. From asset list, click the "Return" button (warning) on assigned assets
2. Set return date
3. Select return condition: Returned, Damaged, or Lost
4. Add remarks describing the condition
5. Click "Process Return"
6. Asset status updates based on return condition

### Viewing Asset History:
1. Click "History" button (primary) on any asset
2. View complete assignment timeline
3. See who assigned, when, and any documents

### Viewing Employee Assets:
1. Click on employee name in "Assigned To" column
2. View current and past assignments
3. See complete asset history for that employee

---

## 🔒 Business Logic & Validations

### Asset Assignment:
- ✅ Only "Available" assets can be assigned
- ✅ Prevents duplicate active assignments
- ✅ Validates employee exists
- ✅ Requires assignment date
- ✅ Optional document upload (PDF, DOC, images)

### Asset Return:
- ✅ Only "Assigned" assets can be returned
- ✅ Requires return date
- ✅ Requires return condition status
- ✅ Updates asset status automatically:
  - Returned → Asset becomes "Available"
  - Damaged → Asset becomes "Maintenance"
  - Lost → Asset becomes "Lost"

### Asset Deletion:
- ✅ Cannot delete currently assigned assets
- ✅ Must return asset first
- ✅ Deletes associated images

---

## 🎨 UI Features

### Dashboard Statistics:
- Total Assets count
- Available Assets count
- Assigned Assets count
- Maintenance Assets count

### Status Badges:
- 🟢 Available (Success/Green)
- 🔵 Assigned (Primary/Blue)
- 🟡 Maintenance (Warning/Yellow)
- 🔴 Lost (Danger/Red)

### Action Buttons:
- 👁️ View asset details
- ➕ Assign asset (green, only for available)
- ➖ Return asset (warning, only for assigned)
- 📜 View history
- ✏️ Edit asset
- 🗑️ Delete asset

---

## 📊 Model Relationships

```php
// Asset Model
Asset::employeeAssets()          → hasMany EmployeeAsset
Asset::currentAssignment()       → hasOne EmployeeAsset (active)
Asset::requests()                → hasMany AssetRequest
Asset::employees()               → belongsToMany Employee (pivot)

// EmployeeAsset Model
EmployeeAsset::employee()        → belongsTo Employee
EmployeeAsset::asset()           → belongsTo Asset
EmployeeAsset::assignedBy()      → belongsTo User

// Employee Model (Enhanced)
Employee::employeeAssets()       → hasMany EmployeeAsset
Employee::currentAssets()        → hasMany EmployeeAsset (active)
Employee::assetRequests()        → hasMany AssetRequest

// AssetRequest Model
AssetRequest::employee()         → belongsTo Employee
AssetRequest::asset()            → belongsTo Asset
AssetRequest::approvedBy()       → belongsTo User
```

---

## 🔧 Scopes & Helper Methods

### Asset Model Scopes:
```php
Asset::available()->get()        → Get available assets
Asset::assigned()->get()         → Get assigned assets
Asset::byCategory('IT')->get()   → Filter by category
```

### Helper Methods:
```php
$asset->isAvailable()            → Check if available
$asset->asset_code               → Auto-generated code
$asset->status_color             → Get badge color
$asset->category_label           → Get category label
```

### EmployeeAsset Scopes:
```php
EmployeeAsset::currentlyAssigned()->get()  → Active assignments
EmployeeAsset::returned()->get()           → Returned assets
```

---

## 🚦 Testing the Module

### Manual Testing Steps:

1. **Create Assets:**
   ```
   - Create 3-5 test assets with different categories
   - Add images to some assets
   - Verify auto-generated asset codes
   ```

2. **Assign Assets:**
   ```
   - Assign assets to different employees
   - Upload assignment documents
   - Verify status changes to "Assigned"
   - Try assigning same asset (should fail)
   ```

3. **Return Assets:**
   ```
   - Return an asset as "Returned" → should become Available
   - Return an asset as "Damaged" → should go to Maintenance
   - Return an asset as "Lost" → should be marked Lost
   ```

4. **View History:**
   ```
   - Check asset history page
   - Verify all assignments are tracked
   - Check employee assets view
   ```

5. **Test Permissions:**
   ```
   - Test with different user roles
   - Verify permission checks work
   ```

---

## 📝 Notes

- All dates are formatted using user's preferred date format
- Amounts are formatted using user's currency settings
- Images are stored in `public/uploads/assets/`
- Documents are stored in `public/uploads/asset_documents/`
- Asset codes are sequential: AST00001, AST00002, etc.
- Multi-tenant support via `created_by` field
- Soft deletes not implemented (can be added if needed)

---

## 🎯 Future Enhancements (Optional)

- [ ] QR code generation for assets
- [ ] Barcode scanning support
- [ ] Asset depreciation calculation
- [ ] Maintenance scheduling
- [ ] Email notifications for assignments
- [ ] Asset booking/reservation system
- [ ] Bulk asset import/export
- [ ] Asset warranty alerts
- [ ] Mobile app integration
- [ ] Asset audit trail

---

## 🐛 Troubleshooting

### Issue: Asset not showing in list
**Solution:** Check `created_by` matches current user's creatorId()

### Issue: Cannot assign asset
**Solution:** Verify asset status is "Available" and no active assignments exist

### Issue: Image not displaying
**Solution:** Check `public/uploads/assets/` directory exists and has write permissions

### Issue: Migration fails
**Solution:** Run `php artisan migrate:rollback` then `php artisan migrate`

---

## 📞 Support

For issues or questions:
1. Check this documentation
2. Review the code comments
3. Test with sample data first
4. Check Laravel logs: `storage/logs/laravel.log`

---

## ✅ Module Status

**COMPLETE** - All core features and bonus features implemented and tested.

- ✅ Database migrations
- ✅ Models with relationships
- ✅ Controllers with business logic
- ✅ Blade views (9 views)
- ✅ Routes configured
- ✅ Validation & security
- ✅ File upload support
- ✅ Permission checks
- ✅ Production-ready code

---

**Version:** 1.0.0  
**Last Updated:** April 15, 2026  
**Laravel Version:** Compatible with Laravel 9+  
**PHP Version:** 8.0+
