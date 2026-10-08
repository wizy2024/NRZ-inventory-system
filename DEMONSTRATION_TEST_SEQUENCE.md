# NRZ Inventory Demonstration Test Sequence

This script uses the repeatable demonstration data created by `php artisan db:seed`.
All demonstration accounts use the password `password`. Change or remove these accounts before production use.

## 1. Reset the demonstration environment

Run this only in a local or training database:

```text
php artisan migrate:fresh --seed
```

Open `/wizy` and sign in with one of these accounts:

| Role                           | Name               | Email                      |
| ------------------------------ | ------------------ | -------------------------- |
| Administrator                  | Test Administrator | admin@example.com          |
| Administrator (legacy account) | Test User          | test@example.com           |
| Inventory Manager              | Thandiwe Moyo      | thandiwe.moyo@example.com  |
| Technician                     | Brian Ncube        | brian.ncube@example.com    |
| Technician                     | Nomsa Dube         | nomsa.dube@example.com     |
| Auditor                        | Rudo Chikwanha     | rudo.chikwanha@example.com |
| Read Only                      | Peter Ndlovu       | peter.ndlovu@example.com   |

## 2. Confirm the seeded coverage

Sign in as the Inventory Manager and verify:

1. Assets contains nine records.
2. All seven departments are represented: IT, Finance, HR, Audit, Security, Traffic, and Marketing.
3. All four locations are represented: Harare, Bulawayo, Rutenga, and Lowveld.
4. Asset statuses are only `Active` or `Decommissioned`.
5. The asset filters work for status, type, department, location, and warranty attention.
6. The dashboard shows active devices, decommissioned assets, open maintenance, locations, and departments.

Expected examples:

- `NRZ-IT-0001`: assigned to Thandiwe Moyo in Harare.
- `NRZ-FIN-0001`: active desktop with an open maintenance record.
- `NRZ-SEC-0002`: decommissioned radio with a missing audit follow-up.

## 3. Demonstrate asset registration and validation

1. Open Assets and select Create.
2. Try to reuse `NRZ-IT-0001` as the asset tag. Confirm that duplicate validation prevents saving.
3. Enter an invalid MAC address such as `ABC123`. Confirm that validation prevents saving.
4. Enter a warranty date before the purchase date. Confirm that validation prevents saving.
5. Set status to Decommissioned without a reason or date. Confirm that both fields are required.
6. Create a valid temporary asset for the Marketing department.
7. Return to the asset list and verify the new record is searchable.

## 4. Demonstrate assignment history

1. Open `NRZ-IT-0001` for editing.
2. Change its location from Harare to Bulawayo and update the assignment notes.
3. Save the record.
4. Select Assignment history from the asset table.
5. Confirm that the original and changed assignment snapshots are visible, including the time and user who changed them.

## 5. Demonstrate QR identification

1. Open the asset table and select the QR code action for `NRZ-IT-0001`.
2. Confirm that the QR label displays the asset tag, serial number, type, and brand.
3. Print or preview the label.
4. Scan the QR code from a device that can reach the configured application URL.
5. Confirm that the signed asset information page opens.
6. Confirm that an unsigned `/device/{id}/info` URL is rejected.

## 6. Demonstrate technician alerts and transfer history

1. Sign in as Nomsa Dube and open the notification bell.
2. Confirm that the seeded open maintenance task `NRZ-FIN-0001` is visible.
3. Sign out and sign in as Thandiwe Moyo.
4. Open Maintenance and edit the `NRZ-FIN-0001` task.
5. Transfer it from Nomsa Dube to Brian Ncube and save.
6. Sign in as Brian Ncube.
7. Confirm that Brian receives a new unread notification linking to the maintenance task.
8. Open Assignment history for the task.
9. Confirm that the original assignment to Nomsa and the transfer to Brian are both recorded.
10. Edit only the description and save. Confirm that no duplicate assignment notification is created.

## 7. Demonstrate maintenance completion and gate pass

1. As Brian Ncube, open the transferred maintenance task.
2. Set the status to Resolved.
3. Enter a resolution date and resolution notes.
4. Save the record.
5. Sign in as Thandiwe Moyo and open Gate passes.
6. Create a gate pass for the same asset.
7. Select the resolved maintenance record belonging to that asset.
8. Enter the collector's name, contact number, identity number, release time, and handover notes.
9. Save the gate pass.
10. Confirm that the printable gate pass contains matching asset, serial, collector, issuer, and maintenance details.

## 8. Demonstrate audit and follow-up

1. Open Audits as Rudo Chikwanha.
2. Create a `Found and verified` audit for `NRZ-IT-0001`.
3. Confirm that expected location and expected assignee are captured automatically.
4. Create a `Wrong location` audit for `NRZ-TRF-0001`.
5. Set follow-up status to Open, assign Thandiwe Moyo, set a due date, and enter follow-up notes.
6. Filter audits by Open and Overdue follow-up.
7. Update the follow-up to In progress, then Resolved.
8. Confirm that the audit history and follow-up fields remain visible.

## 9. Demonstrate reporting

1. Sign in as Thandiwe Moyo.
2. Open Reports and review the inventory distribution by department and location.
3. Create an Inventory summary report.
4. Create a Maintenance report and confirm that pending, in-progress, and resolved counts are shown.
5. Create a Warranty report and review expired, expiring, and covered assets.
6. Confirm that the saved reports display the generator, timestamp, summary, and notes.
7. Download the PDF report and confirm that it contains the asset register and operational summaries.

## 10. Demonstrate permissions

1. As Peter Ndlovu, confirm that assets and maintenance can be viewed.
2. Confirm that Create, Edit, Delete, Reports, Audits, Gate passes, and User management are unavailable.
3. As Brian Ncube, confirm that maintenance can be managed but user management, audits, reports, and gate passes are unavailable.
4. As Rudo Chikwanha, confirm that audits and reports are available but asset editing and gate passes are unavailable.
5. As Test Administrator, confirm that user and role administration is available.

## 11. Acceptance evidence to record

For each completed scenario, record:

- Tester name and role
- Date and environment
- Asset or record identifier
- Expected result
- Actual result
- Screenshot or exported document reference
- Pass or fail decision
- Defect number and corrective action, if failed

The demonstration database is suitable for training and acceptance testing. It must not be treated as real NRZ inventory.
