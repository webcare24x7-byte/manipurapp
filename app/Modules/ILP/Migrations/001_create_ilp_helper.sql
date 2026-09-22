SET NAMES utf8mb4;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS ilp_permit_types (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(40) NOT NULL,
    name VARCHAR(180) NOT NULL,
    short_description VARCHAR(500) NOT NULL,
    purpose_summary TEXT NOT NULL,
    validity_summary VARCHAR(255) DEFAULT NULL,
    sponsor_summary VARCHAR(255) DEFAULT NULL,
    renewal_summary VARCHAR(500) DEFAULT NULL,
    application_summary TEXT DEFAULT NULL,
    official_url VARCHAR(500) DEFAULT NULL,
    source_title VARCHAR(255) DEFAULT NULL,
    source_verified_at DATE DEFAULT NULL,
    status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_ilp_permit_code (code),
    KEY idx_ilp_permit_status (status),
    KEY idx_ilp_permit_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ilp_requirements (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    permit_code VARCHAR(40) NOT NULL,
    requirement_type ENUM('DOCUMENT','INFORMATION','SPONSOR','PROCESS','WARNING') NOT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT DEFAULT NULL,
    required_flag TINYINT(1) NOT NULL DEFAULT 1,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_ilp_req_code (permit_code),
    UNIQUE KEY uq_ilp_req (permit_code,requirement_type,title),
    KEY idx_ilp_req_type (requirement_type),
    CONSTRAINT fk_ilp_req_permit_code FOREIGN KEY (permit_code) REFERENCES ilp_permit_types(code) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ilp_permit_types
(code,name,short_description,purpose_summary,validity_summary,sponsor_summary,renewal_summary,application_summary,official_url,source_title,source_verified_at,sort_order)
VALUES
('TEMPORARY','Temporary Inner Line Permit','For tourists, business travellers and short-term visitors.','Use for short visits such as tourism, short-term travel and business visits.','Generally up to 30 days; non-renewable.','No sponsor is required for the Temporary permit.','Temporary permits cannot be renewed. The official portal advises applicants to use another eligible permit where applicable before expiry.','Online application is available through the official Manipur ILP portal. Eligible temporary applications may also be supported through designated counters and self-service kiosks.','https://manipurilponline.mn.gov.in/forms/temporary-permit-info.aspx','Government of Manipur ILP Portal — Temporary Inner Line Permit','2026-09-22',10),
('REGULAR','Regular Inner Line Permit','For individuals who visit Manipur frequently.','Use for eligible frequent visitors and longer stays requiring a Regular ILP.','Generally up to 90 days.','A valid Sponsor ID from a permanent resident/domicile of Manipur is required.','Renewal is subject to applicable Government rules; the official portal should be checked for the current procedure.','Online application is available through the official Manipur ILP portal.','https://manipurilponline.mn.gov.in/forms/regular-permit-info.aspx','Government of Manipur ILP Portal — Regular Inner Line Permit','2026-09-22',20),
('SPECIAL','Special Category Inner Line Permit','For government contractors, investors, entrepreneurs, traders and business establishments.','Use for eligible longer-term business, investment, trading or establishment activities in Manipur.','Generally up to 3 years.','A valid Sponsor ID from a permanent resident/domicile of Manipur is required.','May be renewed according to applicable Government rules.','Online application is available through the official Manipur ILP portal.','https://manipurilponline.mn.gov.in/forms/special-permit-info.aspx','Government of Manipur ILP Portal — Special Category Inner Line Permit','2026-09-22',30),
('LABOUR','Labour Inner Line Permit','For labourers engaged by registered firms, agencies, contractors or individuals for work in Manipur.','Used for labour groups engaged for work such as construction, with the application submitted by the registered engaging entity.','Refer to the current permit and Government rules for the applicable validity.','An Agency ID is required. The registered firm, agency, contractor or individual submits the application.','Renewal and validity are governed by the current ILP rules and portal workflow.','Labourers do not submit the Labour ILP application directly; the registered engaging agency submits it.','https://manipurilponline.mn.gov.in/forms/labour-permit-info.aspx','Government of Manipur ILP Portal — Labour Inner Line Permit','2026-09-22',40),
('HALF_YEARLY','Half-Yearly Working Inner Line Permit','For workers/labourers in the unorganized sector and temporary employees of registered private companies, firms or institutions.','Use for eligible temporary employment or working arrangements in Manipur.','Up to 180 days or the approved employment period, whichever is earlier; one extension may be allowed up to the applicable ceiling.','Sponsor ID is required. Sponsorship may be by a permanent resident or an authorized representative of the registered company, firm or institution.','One extension of up to 180 days is allowed under the current portal guidance; total stay cannot exceed 360 days and a fresh permit requires a 30-day cooling-off period.','The applicant proceeds through the official portal with a valid sponsor.','https://manipurilponline.mn.gov.in/forms/half-yearly-permit-info.aspx','Government of Manipur ILP Portal — Half-Yearly Working Inner Line Permit','2026-09-22',50),
('ANNUAL','Annual Working Inner Line Permit','For regular employees of registered private companies, firms and institutions.','Use for eligible regular employment in Manipur.','Up to 360 days or the approved employment period, whichever is earlier.','Sponsor ID is required and sponsorship is by the Managing Director, Proprietor or authorized representative of the registered organization.','The permit is not extendable; the official portal states that a fresh application may be made after expiry with a minimum one-day gap.','The employee applies through the official portal with the required sponsor.','https://manipurilponline.mn.gov.in/forms/annual-work-permit-info.aspx','Government of Manipur ILP Portal — Annual Working Inner Line Permit','2026-09-22',60)
ON DUPLICATE KEY UPDATE
name=VALUES(name),short_description=VALUES(short_description),purpose_summary=VALUES(purpose_summary),validity_summary=VALUES(validity_summary),sponsor_summary=VALUES(sponsor_summary),renewal_summary=VALUES(renewal_summary),application_summary=VALUES(application_summary),official_url=VALUES(official_url),source_title=VALUES(source_title),source_verified_at=VALUES(source_verified_at),sort_order=VALUES(sort_order),status='Active';

INSERT INTO ilp_requirements (permit_code,requirement_type,title,description,required_flag,sort_order) VALUES
('TEMPORARY','DOCUMENT','Government-issued photo ID','Keep a valid Government-issued photo identity document ready for verification.',1,10),
('TEMPORARY','INFORMATION','Applicant details','Applicant name, parent/guardian name, date of birth, gender, state, district, police station and address.',1,20),
('TEMPORARY','INFORMATION','Contact details','Active mobile number and email address as required by the current application form.',1,30),
('TEMPORARY','INFORMATION','Stay and visit details','Period of stay, place of stay in Manipur, purpose of visit and entry gate.',1,40),
('TEMPORARY','DOCUMENT','Passport photograph','The official workflow may require a passport photograph and supporting documents after form submission.',1,50),
('TEMPORARY','WARNING','Use the official portal for the current form','Fields, document requirements and fees can change. Verify the current official portal before submitting.',1,60),
('REGULAR','SPONSOR','Valid Sponsor ID','A sponsor who is a permanent resident/domicile of Manipur is required.',1,10),
('REGULAR','DOCUMENT','Identity proof','The official application form requires an identity proof and identity number.',1,20),
('REGULAR','INFORMATION','Applicant and address details','Applicant, contact, state, district, police station and address information are required by the current form.',1,30),
('REGULAR','INFORMATION','Purpose and stay details','Purpose, place and period of stay in Manipur are required by the current application form.',1,40),
('REGULAR','WARNING','Check current Government rules','Validity, renewal and documentary requirements should be confirmed against the latest official guidance.',1,50),
('SPECIAL','SPONSOR','Valid Sponsor ID','A sponsor who is a permanent resident/domicile of Manipur is required.',1,10),
('SPECIAL','DOCUMENT','Identity proof','The official application form requires identity proof and identity number.',1,20),
('SPECIAL','INFORMATION','Business or visit purpose','The applicant should be prepared to provide the purpose and relevant stay details.',1,30),
('SPECIAL','WARNING','Check current Government rules','Long-term business and investment cases may have additional requirements under current rules.',1,40),
('LABOUR','SPONSOR','Agency ID','The registered firm, agency, contractor or individual engaging labour must have the required Agency ID.',1,10),
('LABOUR','DOCUMENT','Labourer identity and supporting documents','The engaging agency must collect the labourers\' personal details and supporting documents.',1,20),
('LABOUR','PROCESS','Agency submits the application','Labourers cannot submit the Labour ILP application directly through the portal.',1,30),
('LABOUR','WARNING','Confirm current labour requirements','The engaging agency should verify the latest portal requirements before submission.',1,40),
('HALF_YEARLY','SPONSOR','Valid Sponsor ID','A valid Sponsor ID is mandatory before applying.',1,10),
('HALF_YEARLY','DOCUMENT','Employment/work details','Keep the relevant employment or work information and supporting documents ready.',1,20),
('HALF_YEARLY','INFORMATION','Applicant details','Personal identity, contact and stay information are required by the application workflow.',1,30),
('HALF_YEARLY','WARNING','Cooling-off and extension rules','The current portal states a maximum 180-day period, one extension up to the applicable ceiling, and a 30-day cooling-off period before a fresh permit.',1,40),
('ANNUAL','SPONSOR','Valid Sponsor ID','A valid Sponsor ID is mandatory.',1,10),
('ANNUAL','DOCUMENT','Employment details','Keep employment/engagement information and supporting documents ready.',1,20),
('ANNUAL','INFORMATION','Applicant details','Personal identity, contact and stay information are required by the application workflow.',1,30),
('ANNUAL','WARNING','Non-extendable permit','The current portal states that the Annual Working Permit is not extendable and a fresh application can be made after expiry with a minimum one-day gap.',1,40)
ON DUPLICATE KEY UPDATE title=VALUES(title),description=VALUES(description),required_flag=VALUES(required_flag),sort_order=VALUES(sort_order),status='Active';

COMMIT;
