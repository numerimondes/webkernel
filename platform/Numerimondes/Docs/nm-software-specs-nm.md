## Database Schema - Development Order

---

## 1. Core Identity & Access

### users [OK GOOD]
```
id | name         | email                | email_verified_at      | password      | remember_token | created_at          | updated_at
5  | System Admin | admin@webkernel.com  | 2024-01-01 00:00:00    | $10$92IX..... | NULL           | 2024-01-01 00:00:00 | 2024-01-01 00:00:00
```

### organizations
```
id  | name              | slug              | created_at
101 | Acme Corp         | acme-corp         | 2024-01-01 00:00:00
102 | TechStart Inc     | techstart-inc     | 2024-03-15 00:00:00
```

### users_organisations
```
id  | user_id | name       | address           
101 | 42      | Acme CORP  | 221B Baker Street 
102 | 43      | Techstart  | 11 Rue de Cambrai 
103 | 44      | ARTAME     | Alice Land        
```

### users_organisations_role
```
id | org_id | user_id | role_name | permissions                          | created_at
1  | 101    | 42      | admin     | all                                  | 2024-01-01 00:00:00
2  | 102    | 43      | admin     | all                                  | 2024-03-15 00:00:00
3  | 101    | 44      | developer | manage_instances,view_modules        | 2024-01-01 00:00:00
4  | 101    | 45      | viewer    | view_modules,view_instances          | 2024-02-01 00:00:00
```

---

## 2. Core Software Registry

### softwares
```
id | org_id | name      | slug      | install_path   | namespace | is_active | created_by | updated_by | created_at          | updated_at
1  | NULL   | Webkernel | webkernel | webkernel/src/ | Webkernel | 1         | 1          | 1          | 2024-01-01 00:00:00 | 2024-01-01 00:00:00
```

### software_cores
```
id | software_id | name              | version | zip_path                               | install_path   | namespace | hash                             | validation_status | metadata                      | created_at          | updated_at
1  | 1           | Webkernel Origin  | 1.0.1   | ["modules/webkernel_origin_101.zip"]   | webkernel/src/ | Webkernel | 8f3c2e7d1b4e8f0a6c9b2d3f1e4a7c8  | validated          | {"size":"1.2MB"}              | 2024-01-01 00:00:00 | 2024-01-01 00:00:00
2  | 1           | Webkernel Origin  | 1.9.5   | ["modules/webkernel_origin_195.zip"]   | webkernel/src/ | Webkernel | b7d8e4f2c3a1d9e0f5a6b3c2e8d7f9b2 | validated          | {"size":"1.4MB"}              | 2024-01-15 00:00:00 | 2024-01-15 00:00:00
3  | 1           | Webkernel Horizon | 2.0.0   | ["modules/webkernel_horizon_200.zip"]  | webkernel/src/ | Webkernel | c1e2f3d4a5b6c7d8e9f0a1b2c3d4e5c3 | pending            | {"size":"2.0MB","notes":"new"}| 2024-02-01 00:00:00 | 2024-02-01 00:00:00
```

---

## 3. Vendors

### vendors
```
id | user_id | org_id | name              | slug              | is_official | is_verified | commission_rate | created_at
1  | NULL    | NULL   | Webkernel Labs    | webkernel-labs    | 1           | 1           | 0.00            | 2024-01-01 00:00:00
2  | 42      | 101    | DevShop Solutions | devshop-solutions | 0           | 1           | 0.30            | 2024-06-15 10:00:00
3  | 87      | NULL   | John Doe          | john-doe          | 0           | 0           | 0.30            | 2025-01-10 14:30:00
```

### commission_history
```
id | vendor_id | effective_from      | effective_to        | commission_rate | changed_by_user_id | changed_at
1  | 2         | 2024-06-15 00:00:00 | NULL                | 0.30            | 5                  | 2024-06-15 10:00:00
2  | 3         | 2025-01-10 00:00:00 | NULL                | 0.30            | 5                  | 2025-01-10 14:30:00
```

---

## 4. Modules Marketplace

### modules
```
id | vendor_id | core_id | slug          | name          | version | type       | status    | zip_path            | install_path   | namespace | hash                             | created_at          | published_at
1  | 1         | 3       | crm-pro       | CRM Pro       | 1.0.0   | ready_made | published | modules/crm_100.zip | webkernel/src/ | Webkernel | d1e2f3a4b5c6d7e8f9a0b1c2d3e4f5a1 | 2024-01-01 00:00:00 | 2024-01-15 00:00:00
2  | 2         | 3       | custom-erp    | Custom ERP    | 1.0.0   | custom     | published | modules/erp_100.zip | webkernel/src/ | Webkernel | e2f3a4b5c6d7e8f9a0b1c2d3e4f5a6b2 | 2024-06-20 00:00:00 | 2024-07-01 00:00:00
3  | 3         | 2       | analytics-pro | Analytics Pro | 2.1.0   | ready_made | pending   | modules/analytics.z | webkernel/src/ | Webkernel | f3a4b5c6d7e8f9a0b1c2d3e4f5a6b7c3 | 2025-01-15 00:00:00 | NULL
4  | 1         | 3       | crm-pro       | CRM Pro       | 1.1.0   | ready_made | published | modules/crm_110.zip | webkernel/src/ | Webkernel | a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d1 | 2025-02-01 00:00:00 | 2025-02-15 00:00:00
```

### module_versions
```
id | module_base_id | version | core_id | status    | changelog                           | is_compatible_with_previous | release_date        | deprecated_at
1  | 1              | 1.0.0   | 3       | stable    | Initial release                     | 1                           | 2024-01-15 00:00:00 | NULL
2  | 1              | 1.1.0   | 3       | stable    | Added export feature, bug fixes     | 1                           | 2025-02-15 00:00:00 | NULL
3  | 2              | 1.0.0   | 3       | stable    | Custom ERP first version            | 1                           | 2024-07-01 00:00:00 | NULL
4  | 3              | 2.0.0   | 2       | beta      | Major rewrite with new analytics    | 0                           | 2025-01-15 00:00:00 | NULL
5  | 3              | 2.1.0   | 2       | pending   | Performance improvements            | 1                           | NULL                | NULL
```

### module_core_compatibility
```
id | module_id | core_id | min_core_version | max_core_version | tested_at           | is_verified
1  | 1         | 3       | 2.0.0            | NULL             | 2024-01-14 00:00:00 | 1
2  | 1         | 2       | 1.9.0            | 1.9.5            | 2024-01-14 00:00:00 | 1
3  | 2         | 3       | 2.0.0            | NULL             | 2024-06-30 00:00:00 | 1
4  | 3         | 2       | 1.8.0            | 1.9.5            | 2025-01-15 00:00:00 | 0
```

### module_dependencies
```
id | module_id | depends_on_module_id | dependency_type | min_version | is_required | created_at
1  | 2         | 1                    | functional      | 1.0.0       | 1           | 2024-06-20 00:00:00
2  | 3         | 1                    | integration     | 1.0.0       | 0           | 2025-01-15 00:00:00
```

### module_integrations
```
id | module_id | integrates_with_module_id | integration_type | description                    | is_bidirectional | created_at
1  | 1         | 2                         | data_sync        | CRM contacts sync to ERP       | 1                | 2024-07-01 00:00:00
2  | 3         | 1                         | analytics        | Analytics dashboard for CRM    | 0                | 2025-01-15 00:00:00
3  | 3         | 2                         | reporting        | ERP financial reports          | 0                | 2025-01-15 00:00:00
```

### module_validations
```
id | module_id | reviewer_user_id | status   | reviewed_at         | notes                          | security_scan_passed | code_review_passed | performance_tested
1  | 1         | 5                | approved | 2024-01-14 16:00:00 | Security scan passed           | 1                    | 1                  | 1
2  | 2         | 5                | approved | 2024-06-30 12:00:00 | Code review OK                 | 1                    | 1                  | 1
3  | 3         | NULL             | pending  | NULL                | Awaiting review                | 0                    | 0                  | 0
4  | 4         | 5                | approved | 2025-02-14 10:00:00 | Version 1.1.0 approved         | 1                    | 1                  | 1
```

### module_contracts
```
id | module_id | version | contract_type | terms_url                           | effective_from      | is_active
1  | 1         | 1.0     | eula          | https://cdn.wk/contracts/crm-eula-1 | 2024-01-15 00:00:00 | 1
2  | 2         | 1.0     | custom        | https://cdn.wk/contracts/erp-cust-1 | 2024-07-01 00:00:00 | 1
3  | 1         | 1.1     | eula          | https://cdn.wk/contracts/crm-eula-2 | 2025-02-15 00:00:00 | 1
```

---

## 5. Pricing & Tax

### module_pricing_plans
```
id | module_id | plan_type    | name              | price_amount | price_currency | billing_cycle | custom_rate_per_day | is_active | features_json                                    | max_users | max_instances
1  | 1         | subscription | Monthly           | 49.00        | USD            | monthly       | NULL                | 1         | {"support": "email", "updates": true}            | NULL      | NULL
2  | 1         | subscription | Annual            | 490.00       | USD            | yearly        | NULL                | 1         | {"support": "priority", "updates": true}         | NULL      | NULL
3  | 1         | perpetual    | Lifetime          | 999.00       | USD            | once          | NULL                | 1         | {"support": "lifetime", "updates": true}         | NULL      | NULL
4  | 2         | custom       | Custom Dev        | NULL         | USD            | custom        | 450.00              | 1         | {"support": "dedicated", "custom_dev": true}     | NULL      | NULL
5  | 3         | freemium     | Free Tier         | 0.00         | USD            | monthly       | NULL                | 1         | {"support": "community", "limited_features": 1}  | 5         | 1
6  | 3         | subscription | Pro Tier          | 99.00        | USD            | monthly       | NULL                | 1         | {"support": "email", "full_features": true}      | NULL      | NULL
```

### tax_rules
```
id | country_code | region | tax_name | tax_rate | is_active | effective_from      | effective_to
1  | US           | CA     | Sales Tax| 0.0725   | 1         | 2024-01-01 00:00:00 | NULL
2  | FR           | NULL   | VAT      | 0.20     | 1         | 2024-01-01 00:00:00 | NULL
3  | GB           | NULL   | VAT      | 0.20     | 1         | 2024-01-01 00:00:00 | NULL
4  | DE           | NULL   | VAT      | 0.19     | 1         | 2024-01-01 00:00:00 | NULL
```

### discount_coupons
```
id | code       | discount_type | discount_value | applies_to_module_id | applies_to_plan_id | min_purchase_amount | max_uses | times_used | valid_from          | valid_until         | is_active
1  | LAUNCH50   | percentage    | 50.00          | NULL                 | NULL               | NULL                | 100      | 23         | 2024-01-01 00:00:00 | 2024-03-31 23:59:59 | 0
2  | ANNUAL20   | percentage    | 20.00          | NULL                 | 2                  | NULL                | NULL     | 45         | 2024-01-01 00:00:00 | NULL                | 1
3  | CUSTOM100  | fixed         | 100.00         | 2                    | NULL               | 1000.00             | 10       | 3          | 2024-06-01 00:00:00 | 2024-12-31 23:59:59 | 1
```

---

## 6. Purchases & Licenses

### module_purchases
```
id | module_id | pricing_plan_id | org_id | purchased_by_user_id | purchase_amount | tax_amount | discount_amount | commission_amount | vendor_amount | purchased_at        | status    | coupon_id
1  | 1         | 2               | 101    | 42                   | 490.00          | 0.00       | 0.00            | 0.00              | 490.00        | 2025-01-01 00:00:00 | completed | NULL
2  | 2         | 4               | 102    | 43                   | 4500.00         | 0.00       | 100.00          | 1350.00           | 3150.00       | 2025-02-01 00:00:00 | completed | 3
3  | 1         | 1               | 102    | 43                   | 49.00           | 0.00       | 0.00            | 0.00              | 49.00         | 2025-02-15 00:00:00 | active    | NULL
```

### module_licenses
```
id | purchase_id | module_id | org_id | licence_key              | starts_at           | expires_at          | status     | auto_renew | created_at
1  | 1           | 1         | 101    | MODLIC-CRM-101-ABC123    | 2025-01-01 00:00:00 | 2026-01-01 00:00:00 | active     | 1          | 2025-01-01 00:00:00
2  | 2           | 2         | 102    | MODLIC-ERP-102-XYZ789    | 2025-02-01 00:00:00 | NULL                | active     | 0          | 2025-02-01 00:00:00
3  | 3           | 1         | 102    | MODLIC-CRM-102-DEF456    | 2025-02-15 00:00:00 | 2025-03-15 00:00:00 | active     | 1          | 2025-02-15 00:00:00
4  | 1           | 1         | 101    | MODLIC-CRM-101-ABC123    | 2024-01-01 00:00:00 | 2025-01-01 00:00:00 | expired    | 1          | 2024-01-01 00:00:00
```

### license_contract_acceptances
```
id | license_id | contract_id | accepted_by_user_id | accepted_at         | ip_address
1  | 1          | 1           | 42                  | 2025-01-01 00:00:00 | 203.0.113.45
2  | 2          | 2           | 43                  | 2025-02-01 00:00:00 | 198.51.100.23
3  | 3          | 1           | 43                  | 2025-02-15 00:00:00 | 198.51.100.23
```

### license_status_history
```
id | license_id | old_status  | new_status | changed_by_user_id | reason                        | changed_at
1  | 4          | active      | expired    | NULL               | Automatic expiration          | 2025-01-01 00:00:00
2  | 1          | pending     | active     | 42                 | Payment confirmed             | 2025-01-01 00:00:00
3  | 2          | active      | suspended  | 5                  | Payment failed                | 2025-03-15 00:00:00
4  | 2          | suspended   | active     | 43                 | Payment resolved              | 2025-03-16 00:00:00
```

---

## 7. Instances & Deployments

### instances
```
id | org_id | name              | environment | domain                | core_id | is_active | created_at
1  | 101    | Acme Production   | production  | app.acme.com          | 3       | 1         | 2025-01-01 00:00:00
2  | 101    | Acme Staging      | staging     | staging.acme.com      | 3       | 1         | 2025-01-01 00:00:00
3  | 102    | TechStart Main    | production  | app.techstart.com     | 2       | 1         | 2025-02-01 00:00:00
```

### instance_modules
```
id | instance_id | module_license_id | module_id | module_hash                      | activated_at        | is_active | activated_by_user_id
1  | 1           | 1                 | 1         | d1e2f3a4b5c6d7e8f9a0b1c2d3e4f5a1 | 2025-01-01 10:00:00 | 1         | 42
2  | 2           | 1                 | 1         | d1e2f3a4b5c6d7e8f9a0b1c2d3e4f5a1 | 2025-01-02 09:00:00 | 1         | 44
3  | 3           | 2                 | 2         | e2f3a4b5c6d7e8f9a0b1c2d3e4f5a6b2 | 2025-02-01 11:00:00 | 1         | 43
4  | 3           | 3                 | 1         | d1e2f3a4b5c6d7e8f9a0b1c2d3e4f5a1 | 2025-02-15 14:00:00 | 1         | 43
```

### instance_module_activation_logs
```
id | instance_module_id | event_type   | status  | error_message | performed_by_user_id | performed_at        | metadata_json
1  | 1                  | activation   | success | NULL          | 42                   | 2025-01-01 10:00:00 | {"ip": "203.0.113.45"}
2  | 2                  | activation   | success | NULL          | 44                   | 2025-01-02 09:00:00 | {"ip": "203.0.113.46"}
3  | 3                  | activation   | failed  | Hash mismatch | 43                   | 2025-02-01 10:55:00 | {"expected": "abc", "got": "xyz"}
4  | 3                  | activation   | success | NULL          | 43                   | 2025-02-01 11:00:00 | {"retry": 1}
```

---

## 8. Subscriptions & Renewals

### subscription_renewals
```
id | module_license_id | renewed_at          | amount | tax_amount | next_billing_date   | status    | payment_method_id
1  | 1                 | 2025-01-01 00:00:00 | 490.00 | 0.00       | 2026-01-01 00:00:00 | active    | pm_123abc
2  | 3                 | 2025-02-15 00:00:00 | 49.00  | 0.00       | 2025-03-15 00:00:00 | active    | pm_456def
3  | 3                 | 2025-03-15 00:00:00 | 49.00  | 0.00       | 2025-04-15 00:00:00 | pending   | pm_456def
```

---

## 9. Revenue & Payouts

### vendor_payouts
```
id | vendor_id | period_start        | period_end          | total_sales | commission_amount | payout_amount | status    | paid_at             | payment_reference
1  | 1         | 2025-01-01 00:00:00 | 2025-01-31 23:59:59 | 539.00      | 0.00              | 539.00        | paid      | 2025-02-05 00:00:00 | PAY-2025-01-WKL
2  | 2         | 2025-02-01 00:00:00 | 2025-02-28 23:59:59 | 4500.00     | 1350.00           | 3150.00       | pending   | NULL                | NULL
```

---

## 10. Module Updates & Migrations

### module_update_flows
```
id | from_module_id | to_module_id | update_type | migration_script_path              | rollback_script_path               | is_breaking | requires_downtime | estimated_duration_minutes | tested_at           | approved_at
1  | 1              | 4            | minor       | migrations/crm_100_to_110.php      | rollbacks/crm_110_to_100.php       | 0           | 0                 | 5                          | 2025-02-10 00:00:00 | 2025-02-14 00:00:00
2  | 3              | NULL         | major       | migrations/analytics_20_to_21.php  | rollbacks/analytics_21_to_20.php   | 1           | 1                 | 30                         | 2025-01-12 00:00:00 | NULL
```

### instance_module_updates
```
id | instance_module_id | update_flow_id | started_at          | completed_at        | status     | error_message | performed_by_user_id
1  | 1                  | 1              | 2025-02-20 10:00:00 | 2025-02-20 10:05:23 | success    | NULL          | 42
2  | 2                  | 1              | 2025-02-21 09:00:00 | 2025-02-21 09:04:56 | success    | NULL          | 44
3  | 4                  | 1              | 2025-02-22 14:00:00 | NULL                | in_progress| NULL          | 43
```

---

## 11. Analytics & Monitoring

### module_usage_metrics
```
id | instance_module_id | metric_date | active_users | api_calls | errors | avg_response_time_ms | total_data_processed_mb
1  | 1                  | 2025-01-01  | 23           | 1547      | 2      | 145                  | 234.5
2  | 1                  | 2025-01-02  | 25           | 1689      | 0      | 132                  | 267.8
3  | 2                  | 2025-01-02  | 3            | 234       | 1      | 98                   | 45.2
4  | 3                  | 2025-02-01  | 12           | 876       | 5      | 210                  | 156.7
```

### module_error_logs
```
id | instance_module_id | error_type       | error_message                    | stack_trace           | occurred_at         | user_id | resolved
1  | 1                  | database         | Connection timeout               | [stack trace data]    | 2025-01-01 14:23:45 | 44      | 1
2  | 3                  | validation       | Invalid input format             | [stack trace data]    | 2025-02-01 09:12:33 | 43      | 1
3  | 3                  | activation       | Hash verification failed         | [stack trace data]    | 2025-02-01 10:55:12 | 43      | 1
```

---

## 12. Notifications

### notifications
```
id | recipient_type | recipient_id | notification_type    | title                              | message                                      | related_entity_type | related_entity_id | is_read | sent_at             | read_at
1  | user           | 42           | license_expiring     | License expiring soon              | Your CRM Pro license expires in 30 days      | license             | 1                 | 1       | 2024-12-02 00:00:00 | 2024-12-02 08:15:00
2  | user           | 43           | renewal_successful   | Subscription renewed               | Your monthly subscription has been renewed   | license             | 3                 | 0       | 2025-03-15 00:00:00 | NULL
3  | organization   | 102          | module_update        | New module version available       | CRM Pro v1.1.0 is now available              | module              | 4                 | 0       | 2025-02-15 00:00:00 | NULL
4  | user           | 42           | validation_required  | Module pending approval            | Your Analytics Pro module awaits review      | module              | 3                 | 0       | 2025-01-15 00:00:00 | NULL
```

### notification_preferences
```
id | user_id | notification_type    | email_enabled | in_app_enabled | sms_enabled
1  | 42      | license_expiring     | 1             | 1              | 0
2  | 42      | renewal_successful   | 1             | 1              | 0
3  | 42      | module_update        | 0             | 1              | 0
4  | 43      | license_expiring     | 1             | 1              | 1
5  | 43      | renewal_successful   | 1             | 1              | 0
```

---

## 13. Audit & Security

### audit_logs
```
id | user_id | org_id | action_type          | entity_type | entity_id | old_value_json           | new_value_json           | ip_address    | user_agent                         | performed_at
1  | 42      | 101    | create               | purchase    | 1         | NULL                     | {"amount": 490}          | 203.0.113.45  | Mozilla/5.0...                     | 2025-01-01 00:00:00
2  | 44      | 101    | activate             | instance    | 2         | NULL                     | {"module_id": 1}         | 203.0.113.46  | Mozilla/5.0...                     | 2025-01-02 09:00:00
3  | 5       | NULL   | update               | module      | 1         | {"status": "pending"}    | {"status": "approved"}   | 10.0.1.5      | Internal/Admin                     | 2024-01-14 16:00:00
4  | 43      | 102    | deactivate           | license     | 2         | {"status": "active"}     | {"status": "suspended"}  | 198.51.100.23 | Mozilla/5.0...                     | 2025-03-15 00:00:00
5  | 5       | NULL   | delete               | coupon      | 1         | {"code": "LAUNCH50"}     | NULL                     | 10.0.1.5      | Internal/Admin                     | 2024-04-01 00:00:00
```
