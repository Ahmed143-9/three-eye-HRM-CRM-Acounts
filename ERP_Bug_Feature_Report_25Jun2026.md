## **ERP SYSTEM** 

## Bug Report & Feature Request 

_Prepared for: Anthropic / Three Eye BD Development Team_ 

|**Report Date**|25 June 2026|
|---|---|
|**Reported By**|J-Alison Group / MinMax|
|**System / URL**|http://hrm-acc.threeeyebd.com|
|**Total Issues**|23 (Bugs + Feature Requests)|



## **1. Executive Summary** 

This document consolidates all identified bugs and feature requests for the ERP system currently under development. Issues span across the Purchase/PO module, Shipment, HRM (Salary, Leave, Transport, Employee Assets), Bill/Accounts, and Daily Accomplishment modules. All items require review and action by the development team. 

## **2. Issue Register** 

_Complete list of all reported bugs and feature requests:_ 

|**#**|**Module / Page**|**Issue Description**|**Type**|**Priority**|
|---|---|---|---|---|
|1|**Purchase / PO**<br>**Page**|In generated PO page, the 'attention'<br>field should pull contact person's<br>email. Currently not being picked up<br>correctly.|Bug|**Medium**|
|2|**Purchase / PO**<br>**Page**|Buyer & Supplier attention should<br>show Contact Person and mobile<br>(phone number). Signatory field<br>cannot be found / not rendering.|Bug|**High**|
|3|**Purchase / PO**<br>**Page**|After editing Client information,<br>previously created Sales Orders do<br>not reflect the updated info.<br>Changes are not propagating.|Bug|**High**|
|4|**Purchase / PO**<br>**Page**|PO page should be placed after PI<br>page in the navigation/workflow<br>order.|Feature<br>Request|**Medium**|
|5|**Purchase / PO**<br>**Page**|PO download generates two pages<br>instead of one. Expected: single<br>page output.|Bug|**Medium**|
|6|**Purchase / PO**<br>**Page**|In the PO 'Prepared by' box, Three<br>Eye's name should appear.<br>Currently an old accounting software<br>name is showing instead.|Bug|**Medium**|



|7|**Purchase / PO**<br>**Page**|PO workflow signature order must<br>be: Acknowledged by (Client) first,<br>then Accepted by (Supplier).<br>Currently incorrect.|Bug|**High**|
|---|---|---|---|---|
|8|**Purchase / PO**<br>**Page**|'Buying Rate' field has not been<br>removed from PO page as<br>requested.|Feature<br>Request|**Low**|
|9|**Shipment /**<br>**Delivery**|On the Delivery page, 'Buying Rate'<br>is becoming Null. It should retain the<br>correct value.|Bug|**High**|
|10|**Bill / Accounts**|Once a Bill is generated, it should<br>NOT be editable from the Accounts<br>side. Currently Bills can still be<br>edited after generation.|Feature<br>Request|**High**|
|11|**Bill / Accounts**|All uploaded files in Accounts are<br>vanishing / disappearing. Uploaded<br>files are not being persisted.|Bug|**High**|
|12|**Bill / Receivable**|Receivable Bill section has an issue<br>(noted, details to be confirmed with<br>team).|Bug|**Medium**|
|13|**HRM / Bill**|When Driver Info entry is done from<br>HRM and linked to a Bill, the Bill<br>status incorrectly shows as 'Paid'.|Bug|**High**|
|14|**HRM / Salary**|Salary calculation is incorrect.<br>Needs review and correction.|Bug|**High**|
|15|**HRM / Salary**|Salary Sheet page needs to be<br>reviewed/fixed.|Bug|**Medium**|
|16|**HRM / Leave**<br>**Management**|On the Leave add page, the 'Leave<br>Holder Name' field is missing.<br>Cannot identify the leave holder.|Bug|**High**|
|17|**HRM / Transport**<br>**Management**|Transport management data is not<br>appearing in the correct location /<br>layout issue.|Bug|**Medium**|
|18|**HRM / Employee**<br>**Asset Setup**|Page error on Employee Asset<br>Setup page. URL: http://hrm-<br>acc.threeeyebd.com/employee-<br>assets|Bug|**High**|
|19|**Daily**<br>**Accomplishment /**<br>**Attendance**|Daily accomplishment field needs to<br>be added. On daily basis, input<br>should require a Date. If omo date is<br>entered, it should not be accepted.<br>The input field must NOT count as<br>attendance in the system.|Feature<br>Request|**Medium**|
|20|**Daily**<br>**Accomplishment /**<br>**Attendance**|Each employee must be able to log<br>in with their own credentials to<br>access the system.|Feature<br>Request|**Medium**|
|21|**Client Details**|File uploading field must accept<br>multiple inputs (multiple file upload).|Feature<br>Request|**High**|



|||Currently only single file upload is<br>supported.|||
|---|---|---|---|---|
|22|**Purchase /**<br>**General**|10% tolerance setting needs to be<br>confirmed/implemented.|Feature<br>Request|**Low**|
|23|**Bill / Approval**|Approved Bill feature/page needs to<br>be reviewed.|Feature<br>Request|**Medium**|



## **3. Detailed Issue Descriptions** 

## **3.1  Purchase / PO Module** 

- PO Page — 'Attention' field in generated PO must capture the contact person's email. Currently the email is not being taken into attention on the generated PO page. 

- PO Page — Buyer & Supplier attention fields must show: Contact Person name and mobile/phone number. The Signatory field cannot be located and is not rendering. 

- PO Page — Client information edits are NOT reflecting on previously created Sales Orders. After editing client info, the old Sales Order pages still show the outdated data. 

- PO Page — Page order: PO page must be placed after the PI page in the navigation flow. 

- PO Page — Downloading a PO currently produces a 2-page output. Expected behaviour: single page only. 

- PO Page — 'Prepared by' box is showing an old accounting software name instead of Three Eye's name. 

- PO Signature Flow — Correct order must be: (1) Acknowledged by [Client], then (2) Accepted by [Supplier]. This order is currently incorrect. 

- PO Page — 'Buying Rate' field needs to be removed from the PO page (not yet done). 

## **3.2  Shipment / Delivery** 

- Delivery Page — 'Buying Rate' value is being set to Null on the Delivery page. The correct rate must be retained and not wiped. 

## **3.3  Bill / Accounts** 

- Once a Bill is generated and pushed to Accounts, the Accounts team must NOT be able to edit it. Currently this restriction is not enforced. 

- All uploaded files under Accounts are disappearing (vanishing). File persistence is broken — uploaded files are not being saved/retained. 

- Receivable Bill — issue exists, details to be clarified with the team. 

## **3.4  HRM Module** 

- HRM/Bill — When Driver Info is entered from HRM and linked to a Bill, the Bill status incorrectly shows as 'Paid'. This is wrong behaviour. 

- Salary Calculation — The salary calculation logic is incorrect. Needs full review and correction. 

- Salary Sheet Page — Needs review and fixes. 

- Leave Management — On the Leave add page, the 'Leave Holder Name' field is missing entirely. It must be added. 

- Transport Management — Data is not appearing in the correct location in the layout. 

- Employee Asset Setup — Page is throwing an error. URL: http://hrm-acc.threeeyebd.com/employee-assets 

## **3.5  Daily Accomplishment / Attendance** 

- A 'Daily Accomplishment' field needs to be added to the system. 

- Input must require a Date on daily basis. If omo (previous) date is entered, the system should reject it. 

- The accomplishment input field must NOT count toward attendance. Attendance count must remain separate. 

- Each employee must be able to log in with their individual credentials to access their own data. 

## **3.6  Client Details** 

- File Upload — The file uploading field in Client Details must support multiple file uploads simultaneously. Currently only single file upload is available. 

## **3.7  Bill Approval** 

- Approved Bill feature/page needs to be reviewed and implemented properly. 

- 10% tolerance setting needs to be confirmed and implemented in the system. 

## **4. Additional Notes** 

- All issues above were collected from handwritten meeting notes dated 26.03.26 and a J-Alison Group review session. 

- Employee Asset page error URL for direct testing: http://hrm-acc.threeeyebd.com/employeeassets 

- Client trade license PDF reference: https://hrm-acc.threeeyebd.com/uploads/clients/1782299109_MLIL%20E%20Trade%20License %2024-25.pdf 

_This report was prepared on 25 June 2026 for submission to the development team._ 

