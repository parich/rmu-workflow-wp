=== RMU Workflow ===
Contributors:      parich
Tags:              flowchart, shortcode, rmu, workflow, embed
Tested up to:      6.8
Stable tag:        0.1.0
Requires at least: 6.7
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

แสดงรายการ Flowchart จากระบบ RMU Workflow พร้อมค้นหาและกรองด้วย Tag

== Description ==

Plugin สำหรับแสดงรายการ Flowchart จากระบบ RMU Workflow โดยดึงข้อมูลจาก API
แสดงผลเป็น 2-column grid พร้อม neutral palette UI รองรับการค้นหาและกรองด้วย Tag

**คุณสมบัติ:**

* แสดงรายการ Flowchart แบบ 2-column grid (1-column บน mobile)
* ค้นหา Flowchart จาก title และ tag
* กรองด้วย Tag pills
* UI ด้วย neutral palette
* รองรับไฟล์ PDF และ Image พร้อม icon fallback
* Assets โหลดเฉพาะหน้าที่มี shortcode เท่านั้น
* ตั้งค่า API URL ได้จาก Admin Settings

== Installation ==

1. อัปโหลดไฟล์ `rmu-workflow-wp.zip` ผ่าน Plugins → Add New → Upload Plugin
2. กด Activate
3. ไปที่ Settings → RMU Workflow เพื่อตั้งค่า API URL
4. ใส่ shortcode ในหน้าหรือโพสต์ที่ต้องการ

== Usage ==

ใส่ shortcode ต่อไปนี้ในหน้าหรือโพสต์:

`[rmu_workflow dept_id="518"]`

เปลี่ยน `dept_id` เป็น rmuDepartmentId ของหน่วยงานที่ต้องการแสดง

== Configuration ==

ไปที่ **Settings → RMU Workflow** เพื่อตั้งค่า:

* **Endpoint URL** — URL ของ API เช่น `https://workflow.rmu.ac.th/api/embed/flowcharts`
* **Base URL** — URL หลักของระบบ เช่น `https://workflow.rmu.ac.th`

== Frequently Asked Questions ==

= ต้องการ dept_id จากไหน? =

`dept_id` คือ `rmuDepartmentId` ของหน่วยงานในระบบ RMU Workflow รายการ dept_id ที่รองรับ:

**สำนัก/กอง/ศูนย์**

* `2` = กองกลาง
* `3` = กองคลัง
* `4` = กองนโยบายและแผน
* `5` = กองบริหารงานบุคคล
* `6` = กองพัฒนานักศึกษา
* `453` = ศูนย์ยุทธศาสตร์การพัฒนาระบบบริหารจัดการ
* `516` = ศูนย์สหกิจศึกษาและพัฒนาอาชีพ
* `518` = ศูนย์เทคโนโลยีดิจิทัลและนวัตกรรม

**คณะครุศาสตร์**

* `8` = สำนักงานคณบดี (คณะครุศาสตร์)
* `71` = คณะครุศาสตร์ (สาขาวิชา)

**คณะเทคโนโลยีการเกษตร**

* `10` = สำนักงานคณบดี (คณะเทคโนโลยีการเกษตร)
* `84` = คณะเทคโนโลยีการเกษตร (สาขาวิชา)

**คณะมนุษยศาสตร์และสังคมศาสตร์**

* `12` = สำนักงานคณบดี (คณะมนุษยศาสตร์และสังคมศาสตร์)
* `95` = คณะมนุษยศาสตร์และสังคมศาสตร์ (สาขาวิชา)

**คณะวิทยาการจัดการ**

* `14` = สำนักงานคณบดี (คณะวิทยาการจัดการ)
* `108` = คณะวิทยาการจัดการ (สาขาวิชา)

**คณะวิทยาศาสตร์และเทคโนโลยี**

* `16` = สำนักงานคณบดี (คณะวิทยาศาสตร์และเทคโนโลยี)
* `66` = คณะวิทยาศาสตร์และเทคโนโลยี (สาขาวิชา)

**คณะเทคโนโลยีสารสนเทศ**

* `18` = สำนักงานคณบดี (คณะเทคโนโลยีสารสนเทศ)
* `92` = คณะเทคโนโลยีสารสนเทศ (สาขาวิชา)
* `479` = สาขาวิชาเทคโนโลยีสารสนเทศและการสื่อสารเพื่อการศึกษา

= ถ้า API ไม่มีข้อมูลจะแสดงอะไร? =

Plugin จะไม่แสดงอะไรเลย (silent fail) เหมาะสำหรับการ embed ในหน้าต่าง ๆ

= รองรับ mobile ไหม? =

รองรับครับ — แสดง 2 column บน desktop และ 1 column บน mobile (หน้าจอ ≤ 600px)

== Changelog ==

= 0.1.0 =
* Release
