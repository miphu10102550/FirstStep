# FirstStep — Job Platform for New Graduates
สร้างด้วย PHP + MySQL (PDO) สำหรับรันบน XAMPP

## โครงสร้างระบบ
ระบบนี้ครอบคลุม Site Map และ Use Case ทั้งหมดที่ออกแบบไว้:
- **Home** — หน้าแรกพร้อมค้นหางาน
- **Find Jobs** — ค้นหา/กรองงาน, ดูรายละเอียดงาน
- **Companies** — รายชื่อบริษัท, หน้ารายละเอียดบริษัท
- **Tips & Insights** — บทความให้ความรู้
- **For Employers** — สมัครงาน โพสต์งาน จัดการผู้สมัคร
- **Authentication** — สมัครสมาชิก/เข้าสู่ระบบ สำหรับ ผู้สมัครงาน (Job Seeker), นายจ้าง (Employer), และผู้ดูแลระบบ (Admin)

## 1) ติดตั้ง XAMPP
ดาวน์โหลดและติดตั้ง XAMPP จาก https://www.apachefriends.org แล้วเปิด **Apache** และ **MySQL** จาก XAMPP Control Panel

## 2) วางไฟล์โปรเจกต์
คัดลอกโฟลเดอร์ `firststep` ทั้งหมดไปไว้ที่:
- Windows: `C:\xampp\htdocs\firststep`
- macOS: `/Applications/XAMPP/htdocs/firststep`

## 3) สร้างฐานข้อมูล
1. เปิด `http://localhost/phpmyadmin`
2. คลิกแท็บ **Import**
3. เลือกไฟล์ `database.sql` ที่อยู่ในโฟลเดอร์โปรเจกต์ แล้วกด **Go**
   - จะได้ฐานข้อมูลชื่อ `firststep` พร้อมตารางทั้งหมด และบทความตัวอย่าง 2 บทความ

## 4) สร้างบัญชีตัวอย่าง (สำคัญ)
เปิดเบราว์เซอร์ไปที่:
```
http://localhost/firststep/setup-seed.php
```
สคริปต์นี้จะสร้างบัญชีตัวอย่าง (พร้อม password ที่เข้ารหัสถูกต้องด้วย PHP `password_hash`) ให้อัตโนมัติ:

| Role | Email | Password |
|---|---|---|
| Admin | admin@firststep.com | admin123 |
| Employer | hr@techcorp.com | password123 |
| Job Seeker | somchai@example.com | password123 |

**หลังรันเสร็จ ให้ลบไฟล์ `setup-seed.php` ทิ้งเพื่อความปลอดภัย** (หรือจะเก็บไว้ก็ได้ถ้าเป็นเครื่อง dev ส่วนตัว)

## 4.5) อัปเดตฐานข้อมูล (ถ้าเคย import database.sql มาก่อนหน้านี้แล้ว)
ถ้าคุณเคย import `database.sql` และสร้างบัญชีไปแล้วก่อนหน้านี้ ต้องรันสคริปต์นี้เพิ่มเติมครั้งเดียว เพื่อเพิ่มคอลัมน์ใหม่ที่ใช้ในหน้าโปรไฟล์/แดชบอร์ดที่อัปเดต (first_name, last_name, avatar, birthdate, gender, address ฯลฯ):
```
http://localhost/firststep/migrate-profile-fields.php
```
(ถ้าเป็นการติดตั้งใหม่ตั้งแต่ต้น ไม่จำเป็นต้องรันไฟล์นี้ เพราะ `database.sql` มีคอลัมน์เหล่านี้ครบอยู่แล้ว)

## 5) เข้าใช้งานเว็บไซต์
```
http://localhost/firststep/
```

## 6) ตรวจสอบการตั้งค่าฐานข้อมูล (ถ้าจำเป็น)
ไฟล์ `config/database.php` ตั้งค่าเริ่มต้นไว้สำหรับ XAMPP ปกติ (user: `root`, ไม่มี password):
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'firststep');
define('DB_USER', 'root');
define('DB_PASS', '');
```
ถ้า MySQL ของคุณตั้ง password ไว้ ให้แก้ค่า `DB_PASS` ตามจริง

## โครงสร้างไฟล์หลัก
```
firststep/
├── config/database.php          ตั้งค่าการเชื่อมต่อฐานข้อมูล
├── includes/                    header, footer, auth helper
├── admin/                       หน้าจอผู้ดูแลระบบ (จัดการ users, companies, jobs, articles)
├── assets/css/style.css         สไตล์หลักของเว็บ (โทนสีเขียวตาม FirstStep)
├── uploads/resumes/             ไฟล์เรซูเม่ที่ผู้สมัครอัปโหลด
├── database.sql                 โครงสร้างฐานข้อมูล + บทความตัวอย่าง
├── setup-seed.php               สคริปต์สร้างบัญชีตัวอย่าง (รันครั้งเดียว)
├── index.php                    หน้าแรก
├── jobs.php / job-detail.php    ค้นหางาน / รายละเอียดงาน
├── companies.php / company-detail.php
├── tips.php / article-detail.php
├── employers.php                หน้าแนะนำสำหรับนายจ้าง
├── register.php, register-jobseeker.php, register-employer.php
├── login.php, logout.php
├── dashboard-jobseeker.php, profile.php, apply.php
└── dashboard-employer.php, post-job.php, edit-job.php,
    manage-jobs.php, applicants.php, company-profile.php
```

## ฟีเจอร์ตาม Use Case Diagram
**ผู้ใช้ใหม่ / ผู้สมัครงาน (Job Seeker):**
- สมัครสมาชิก / เข้าสู่ระบบ / ออกจากระบบ
- ค้นหางาน, กรองงาน, ดูรายละเอียดงาน
- สมัครงาน (แนบเรซูเม่ + จดหมายสมัครงาน)
- แก้ไขโปรไฟล์ส่วนตัว
- ดูสถานะใบสมัครของตนเอง

**นายจ้าง (Employer):**
- สมัครสมาชิก / เข้าสู่ระบบ พร้อมสร้างข้อมูลบริษัท
- โพสต์ประกาศงาน / แก้ไข / ปิด-เปิดรับสมัคร / ลบ
- ดูและจัดการผู้สมัคร (เปลี่ยนสถานะ: pending, reviewed, accepted, rejected)
- แก้ไขโปรไฟล์บริษัท

**ผู้ดูแลระบบ (Admin):**
- แดชบอร์ดภาพรวมระบบ
- จัดการผู้ใช้ทั้งหมด (แบน/ยกเลิกแบน/ลบ)
- จัดการบริษัท และประกาศงานทั้งหมด
- จัดการบทความ Tips & Insights

## หมายเหตุ
- ระบบนี้เป็น MVP ที่ครอบคลุมฟังก์ชันหลักตาม Site Map ที่ให้มา ส่วนฟีเจอร์เสริม เช่น ระบบชำระเงิน (PayPal/Credit Payment) และ Identity Provider ตาม Use Case Diagram ยังไม่ได้ implement เนื่องจากต้องเชื่อมต่อ API ภายนอก — แจ้งได้หากต้องการให้เพิ่มเติมส่วนนี้

## หน้าที่อัปเดตล่าสุด (Sidebar Dashboard Redesign)
- **profile.php** — หน้าสร้างโปรไฟล์ผู้ใช้ (job seeker) แบบแท็บด้านซ้าย: ข้อมูลส่วนตัว / การศึกษา / ประสบการณ์ทำงาน พร้อมอัปโหลดรูปโปรไฟล์
- **admin/index.php** และหน้า admin อื่น ๆ — ปรับเป็น sidebar เมนูด้านซ้ายสไตล์ dashboard พร้อมสถิติ, กราฟ, และ widget ต่าง ๆ. เพิ่มหน้าใหม่: admin/applicants.php (รายชื่อผู้สมัครทั้งระบบ), admin/notifications.php, admin/settings.php
- **dashboard-employer.php** และหน้า employer อื่น ๆ (post-job, manage-jobs, edit-job, applicants, company-profile) — ปรับเป็น sidebar เมนูด้านซ้ายเช่นกัน พร้อมสถิติผู้สมัคร, กราฟวงกลม, กราฟเส้น. เพิ่มหน้าใหม่: applicants-all.php (ผู้สมัครทั้งหมดของบริษัท), employer-notifications.php, employer-settings.php
- โลโก้บริษัทและ thumbnail ในการ์ดต่าง ๆ ใช้ตัวอักษรย่อสี/ไอคอนแทนรูปจริง เพื่อไม่ให้ต้องพึ่งอินเทอร์เน็ต/ลิขสิทธิ์แบรนด์
