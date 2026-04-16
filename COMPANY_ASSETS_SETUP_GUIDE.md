# 🏢 Company Assets Setup - Complete Guide

## 📋 Overview

The **Company Assets Setup** module allows your company to:
1. ✅ Define and manage asset categories
2. ✅ Enter detailed asset information
3. ✅ Track all company assets in one place
4. ✅ Assign assets to employees
5. ✅ Monitor asset status and history

---

## 🎯 Two Main Components

### **1. Asset Categories Setup** 
Define how your company classifies assets (IT, Furniture, Electronics, etc.)

### **2. Asset Entry & Management**
Enter individual asset details and manage them

---

## 🚀 Getting Started

### **Step 1: Setup Asset Categories**

Navigate to: `http://your-domain/asset-categories`

Or from Assets page, click **"Manage Categories"** button.

#### **Option A: Use Default Categories (Recommended)**
Click **"Setup Default Categories"** button to instantly create:
- ✅ IT Equipment (computers, laptops, monitors)
- ✅ Furniture (desks, chairs, cabinets)
- ✅ Electronics (phones, printers, cameras)
- ✅ Vehicles (cars, trucks, bikes)
- ✅ Machinery (industrial machines, tools)
- ✅ Other (miscellaneous assets)

#### **Option B: Create Custom Categories**
1. Click **"Add Category"**
2. Fill in:
   - **Category Name** (e.g., "Office Equipment")
   - **Category Code** (e.g., "OFFICE") - will be uppercase
   - **Description** (optional)
   - **Icon** (Tabler icon class, e.g., "ti ti-device-laptop")
   - **Badge Color** (for visual distinction)
   - **Active** (toggle on/off)
3. Click **"Create Category"**

---

### **Step 2: Enter Company Assets**

Navigate to: `http://your-domain/account-assets`

Click **"Add Asset"** button.

#### **Required Fields:**
- **Asset Name** - Descriptive name (e.g., "MacBook Pro 16-inch")
- **Category** - Select from your categories
- **Condition** - New, Used, Damaged, Under Maintenance
- **Purchase Date** - When the asset was purchased
- **Amount** - Purchase price
- **Status** - Available, Assigned, Lost, Maintenance

#### **Optional Fields:**
- **Warranty/Support Until** - Warranty expiration date
- **Manufacturer** - Brand/maker
- **Model Number** - Specific model
- **Serial Number** - Unique serial number
- **Location** - Where the asset is located
- **Warranty Until** - Warranty end date
- **Description** - Additional details
- **Asset Image** - Upload photo of the asset

Click **"Create Asset"** to save.

**Note:** Asset code is auto-generated (e.g., AST00001, AST00002)

---

## 📊 Asset Categories Management

### **View Categories**
URL: `/asset-categories`

Shows table with:
- Icon
- Name
- Code (badge)
- Description
- Total Assets count
- Available count
- Assigned count
- Status (Active/Inactive)
- Actions (Edit/Delete)

### **Edit Category**
1. Click edit icon (pencil)
2. Modify fields
3. Click "Update Category"

### **Delete Category**
- Can only delete if NO assets are using that category
- Click delete icon (trash)
- Confirm deletion

---

## 💼 Asset Entry Examples

### **Example 1: IT Equipment**
```
Name: MacBook Pro 16"
Category: IT Equipment
Condition: New
Status: Available
Purchase Date: 2025-01-15
Amount: $2,499.99
Manufacturer: Apple
Model Number: MRW13
Serial Number: C02XK8WPJHD4
Location: IT Storage Room
Warranty Until: 2027-01-15
Description: M3 Max chip, 36GB RAM, 1TB SSD
Image: [Upload photo]
```

### **Example 2: Furniture**
```
Name: Ergonomic Office Chair
Category: Furniture
Condition: New
Status: Available
Purchase Date: 2025-03-05
Amount: $599.00
Manufacturer: Herman Miller
Model Number: Aeron
Serial Number: HM2025001
Location: Office Floor 2
Warranty Until: 2035-03-05
Description: Size B, Fully loaded
```

### **Example 3: Vehicle**
```
Name: Toyota Camry 2024
Category: Vehicles
Condition: New
Status: Available
Purchase Date: 2024-06-01
Amount: $35,000.00
Manufacturer: Toyota
Model Number: Camry XSE
Serial Number: 4T1G11AK8PU123456
Location: Company Parking
Warranty Until: 2027-06-01
Description: White, Company executive vehicle
```

---

## 🔗 Quick Access URLs

```
Asset Dashboard:        /account-assets
Asset Categories:       /asset-categories
Add New Asset:          /account-assets/create
Add New Category:       /asset-categories/create
Setup Default Cats:     POST /asset-categories/setup-defaults
```

---

## 📱 Navigation

### **From Main Menu:**
If you have a menu/sidebar, add these links:

```html
<!-- Assets Menu -->
<li class="nav-item">
    <a class="nav-link" href="{{ route('account-assets.index') }}">
        <i class="ti ti-package"></i>
        <span>Asset Management</span>
    </a>
</li>

<!-- Or as submenu -->
<li class="nav-item">
    <a class="nav-link" href="#assetsSubmenu" data-bs-toggle="collapse">
        <i class="ti ti-package"></i>
        <span>Assets</span>
    </a>
    <div class="collapse" id="assetsSubmenu">
        <a href="{{ route('account-assets.index') }}">All Assets</a>
        <a href="{{ route('asset-categories.index') }}">Categories</a>
        <a href="{{ route('account-assets.requests') }}">Requests</a>
    </div>
</li>
```

---

## 🎨 Dashboard Features

### **Asset Dashboard** (`/account-assets`)

**Statistics Cards:**
- 📦 Total Assets
- ✅ Available Assets
- 🔵 Assigned Assets
- 🔧 Maintenance Assets

**Assets Table:**
- Asset Code (auto-generated)
- Name (with image thumbnail)
- Category (badge)
- Condition
- Status (color-coded badge)
- Purchase Date
- Amount
- Assigned To (employee link)
- Actions (View, Assign, Return, History, Edit, Delete)

**Action Buttons:**
- 🟢 Manage Categories - Go to categories setup
- ➕ Add Asset - Create new asset
- 📋 Asset Requests - View pending requests

---

## ✅ Best Practices

### **1. Setup Categories First**
- Use default categories as starting point
- Add custom categories if needed
- Keep categories organized and logical

### **2. Enter Complete Asset Details**
- Always include serial numbers for tracking
- Upload photos for visual identification
- Record warranty information
- Note exact location

### **3. Use Consistent Naming**
```
Good: "MacBook Pro 16-inch M3 Max"
Bad: "Laptop 1"

Good: "Herman Miller Aeron Chair - Size B"
Bad: "Chair"
```

### **4. Regular Updates**
- Update status when assets change
- Record returns promptly
- Track maintenance activities
- Monitor warranty expirations

### **5. Document Everything**
- Upload purchase receipts
- Attach warranty cards
- Keep assignment letters
- Record condition notes

---

## 🔐 Permissions Required

Add these to your roles:

```
manage assets       - View all assets and categories
create assets       - Add new assets
edit assets         - Modify asset details
delete assets       - Remove assets
assign assets       - Assign to employees
return assets       - Process returns
```

---

## 📊 Reporting & Tracking

### **Asset History**
- Click "History" button on any asset
- View complete assignment timeline
- See who had the asset and when
- Track condition changes

### **Employee Assets**
- Click employee name in "Assigned To" column
- View all current assets for that employee
- See past assignments
- Useful for offboarding

### **Category Reports**
- View assets by category
- See available vs assigned per category
- Identify which categories need more assets

---

## 🛠️ Bulk Operations

### **Quick Setup:**
1. Click "Setup Default Categories"
2. Import asset list (CSV - coming soon)
3. Assign multiple assets at once

### **Future Enhancements:**
- [ ] CSV import for bulk asset entry
- [ ] Barcode/QR code generation
- [ ] Bulk assignment
- [ ] Export to Excel/PDF

---

## 💡 Tips & Tricks

### **Asset Codes:**
- Auto-generated sequentially (AST00001, AST00002...)
- Use for physical labeling
- Can print as barcodes/QR codes

### **Categories:**
- Color-code for easy visual identification
- Icons make navigation faster
- Can deactivate unused categories

### **Images:**
- Upload clear photos
- Show serial number in photo
- Include damage photos if applicable

### **Locations:**
- Be specific: "Floor 2, Room 201, Desk 5"
- Update when assets move
- Helps with audits

---

## 🐛 Troubleshooting

### **Issue: Cannot delete category**
**Solution:** Category has assets assigned. Reassign assets to different category or delete them first.

### **Issue: Asset not showing in list**
**Solution:** Check that `created_by` matches your company ID. Assets are company-specific.

### **Issue: Categories dropdown empty**
**Solution:** Create categories first or click "Setup Default Categories".

### **Issue: Cannot assign asset**
**Solution:** Asset status must be "Available". Check if already assigned.

---

## 📋 Checklist for Company Setup

- [ ] Setup asset categories (default or custom)
- [ ] Add company assets with complete details
- [ ] Upload asset images
- [ ] Record serial numbers
- [ ] Set warranty dates
- [ ] Assign assets to employees (if applicable)
- [ ] Train staff on asset management
- [ ] Schedule regular audits
- [ ] Setup permissions for different roles

---

## 🎓 Training Guide

### **For Admins:**
1. Setup categories
2. Enter all company assets
3. Assign permissions to staff
4. Monitor asset usage
5. Conduct regular audits

### **For HR/Managers:**
1. View available assets
2. Assign assets to new employees
3. Process returns during offboarding
4. Track asset history
5. Generate reports

### **For Employees:**
1. View assigned assets
2. Request new assets (if enabled)
3. Report damage/loss
4. Return assets when leaving

---

## 📞 Support

For help with Company Assets Setup:
1. Check this guide
2. Review inline help text in forms
3. Test with sample data first
4. Check Laravel logs: `storage/logs/laravel.log`

---

## 🎉 Summary

Your company now has a complete asset management system:

✅ **Categories** - Organize assets logically  
✅ **Asset Entry** - Detailed information tracking  
✅ **Assignment** - Track who has what  
✅ **History** - Complete audit trail  
✅ **Reports** - View by category, employee, status  
✅ **Documents** - Upload photos and files  
✅ **Multi-tenant** - Company-specific data  

**Start managing your company assets today!** 🚀

---

**Module Version:** 1.0.0  
**Last Updated:** April 15, 2026  
**Status:** ✅ Production Ready
