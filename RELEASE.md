# ขั้นตอนการออก Version ใหม่

## 1. แก้ไข Code

ทำการแก้ไข/เพิ่ม feature ที่ต้องการใน `src/` หรือ `rmu-workflow-wp.php`

---

## 2. อัปเดต Version Number

แก้ไขไฟล์ `rmu-workflow-wp.php` บรรทัดที่ 5 ให้เป็น version ใหม่:

```php
 * Version: 0.2.0
```

> **หลักการตั้ง version (Semantic Versioning):**
> - `MAJOR.MINOR.PATCH` เช่น `1.2.3`
> - **PATCH** (`0.1.x`) — แก้ bug เล็กน้อย ไม่กระทบการใช้งาน
> - **MINOR** (`0.x.0`) — เพิ่ม feature ใหม่ ยังใช้งานร่วมกันได้
> - **MAJOR** (`x.0.0`) — เปลี่ยนแปลงใหญ่ อาจ breaking change

---

## 3. Build และสร้าง ZIP

```bash
npm run build
npm run plugin-zip
```

> ไฟล์ `rmu-workflow-wp.zip` จะถูกสร้างในโฟลเดอร์ root ของ plugin

---

## 4. Commit และ Push

```bash
git add rmu-workflow-wp.php src/ build/
git commit -m "release: v0.2.0"
git push origin master
```

---

## 5. สร้าง GitHub Release

```bash
gh release create v0.2.0 rmu-workflow-wp.zip \
  --title "v0.2.0" \
  --notes "## สิ่งที่เปลี่ยนแปลง
- แก้ไข ...
- เพิ่ม ..."
```

> **สำคัญ:** Tag บน GitHub (`v0.2.0`) ต้องตรงกับ Version ใน `rmu-workflow-wp.php` (`0.2.0`)

---

## 6. ตรวจสอบ

หลัง Release เสร็จ WordPress ของเว็บปลายทางจะตรวจพบ update อัตโนมัติภายใน **6 ชั่วโมง**

หากต้องการให้ตรวจสอบทันที ไปที่:

```
/wp-admin/update-core.php?force-check=1
```

---

## สรุปคำสั่งทั้งหมด (Copy & Paste)

แทนที่ `0.2.0` ด้วย version จริงที่ต้องการ

```bash
npm run build && npm run plugin-zip
git add rmu-workflow-wp.php src/ build/
git commit -m "release: v0.2.0"
git push origin master
gh release create v0.2.0 rmu-workflow-wp.zip --title "v0.2.0" --notes "## Changelog\n- "
```
