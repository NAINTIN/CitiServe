<?php

function document_request_suffix_options()
{
    return ['', 'Jr', 'Sr', 'II', 'III', 'IV'];
}

function document_request_payment_methods()
{
    return [
        'GCash',
        'Maya',
        'Asia United Bank Corporation',
        'Home Credit',
        'Seabank',
        'GrabPay',
        'Union Bank of the Philippines',
        'GoTyme',
        'ShopeePay',
        'BPI Vybe',
    ];
}

function document_request_definitions()
{
    return [
        'Barangay Business Clearance' => [
            'fields' => [
                ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
                ['name' => 'middle_name', 'label' => 'Middle Name', 'type' => 'text', 'required' => true],
                ['name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'required' => true],
                ['name' => 'suffix', 'label' => 'Suffix', 'type' => 'select', 'options' => document_request_suffix_options(), 'required' => false],
                ['name' => 'business_name', 'label' => 'Business Name', 'type' => 'text', 'required' => true],
                ['name' => 'business_address', 'label' => 'Business Address', 'type' => 'text', 'required' => true],
                ['name' => 'business_nature', 'label' => 'Business Nature', 'type' => 'select', 'options' => [
                    'retail', 'food and beverage', 'manufacturing', 'services', 'trading', 'construction', 'transportation and logistics', 'agriculture', 'healthcare', 'education', 'IT/Online Business', 'real estate', 'finance', 'entertainment', 'others'
                ], 'required' => true],
                ['name' => 'business_nature_other', 'label' => 'Business Nature (Others)', 'type' => 'text', 'required' => false, 'required_if' => ['field' => 'business_nature', 'equals' => 'others']],
                ['name' => 'contact_number', 'label' => 'Contact Number', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
            ],
            'required_uploads' => [
                ['key' => 'dti_registration_file', 'label' => 'DTI Registration File'],
                ['key' => 'owner_valid_id', 'label' => 'Owner Valid ID'],
                ['key' => 'proof_of_business_location', 'label' => 'Proof of Business Location'],
            ],
        ],
        'Barangay Clearance' => [
            'fields' => [
                ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
                ['name' => 'middle_name', 'label' => 'Middle Name', 'type' => 'text', 'required' => true],
                ['name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'required' => true],
                ['name' => 'suffix', 'label' => 'Suffix', 'type' => 'select', 'options' => document_request_suffix_options(), 'required' => false],
                ['name' => 'complete_address', 'label' => 'Complete Address', 'type' => 'text', 'required' => true],
                ['name' => 'date_of_birth', 'label' => 'Date of Birth (MM/DD/YYYY)', 'type' => 'date_mmddyyyy', 'required' => true],
                ['name' => 'age', 'label' => 'Age', 'type' => 'number', 'required' => true],
                ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['single', 'married', 'widowed', 'separated', 'annulled'], 'required' => true],
                ['name' => 'citizenship', 'label' => 'Citizenship', 'type' => 'text', 'required' => true],
                ['name' => 'contact_number', 'label' => 'Contact Number', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'purpose_of_clearance', 'label' => 'Purpose of Clearance', 'type' => 'textarea', 'required' => true],
            ],
            'required_uploads' => [
                ['key' => 'valid_id', 'label' => 'Valid ID'],
            ],
        ],
        'Barangay ID' => [
            'fields' => [
                ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
                ['name' => 'middle_name', 'label' => 'Middle Name', 'type' => 'text', 'required' => true],
                ['name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'required' => true],
                ['name' => 'suffix', 'label' => 'Suffix', 'type' => 'select', 'options' => document_request_suffix_options(), 'required' => false],
                ['name' => 'date_of_birth', 'label' => 'Date of Birth (MM/DD/YYYY)', 'type' => 'date_mmddyyyy', 'required' => true],
                ['name' => 'age', 'label' => 'Age', 'type' => 'number', 'required' => true],
                ['name' => 'gender', 'label' => 'Gender', 'type' => 'text', 'required' => true],
                ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['single', 'married', 'widowed', 'separated', 'annulled'], 'required' => true],
                ['name' => 'contact_number', 'label' => 'Contact Number', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
            ],
            'required_uploads' => [
                ['key' => 'valid_id', 'label' => 'Valid ID'],
                ['key' => 'photo_1x1_white_bg', 'label' => '1x1 Photo (White Background)'],
            ],
        ],
        'Barangay Permit (Construction)' => [
            'fields' => [
                ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
                ['name' => 'middle_name', 'label' => 'Middle Name', 'type' => 'text', 'required' => true],
                ['name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'required' => true],
                ['name' => 'suffix', 'label' => 'Suffix', 'type' => 'select', 'options' => document_request_suffix_options(), 'required' => false],
                ['name' => 'complete_address', 'label' => 'Complete Address', 'type' => 'text', 'required' => true],
                ['name' => 'contact_number', 'label' => 'Contact Number', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'activity_nature', 'label' => 'Activity Nature', 'type' => 'select', 'options' => ['New Construction', 'Renovation/Remodeling', 'Repair/Maintenance', 'Extension/Expansion', 'Demolition', 'electrical work', 'plumbing work', 'fencing/perimeter wall'], 'required' => true],
                ['name' => 'estimated_start_date', 'label' => 'Estimated Start Date (MM/DD/YYYY)', 'type' => 'date_mmddyyyy', 'required' => true],
                ['name' => 'estimated_completion_date', 'label' => 'Estimated Completion Date (MM/DD/YYYY)', 'type' => 'date_mmddyyyy', 'required' => true],
            ],
            'required_uploads' => [
                ['key' => 'construction_plan_or_sketch', 'label' => 'Construction Plan or Sketch'],
                ['key' => 'applicant_valid_id', 'label' => 'Applicant Valid ID'],
            ],
        ],
        'Certificate of Indigency' => [
            'fields' => [
                ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
                ['name' => 'middle_name', 'label' => 'Middle Name', 'type' => 'text', 'required' => true],
                ['name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'required' => true],
                ['name' => 'suffix', 'label' => 'Suffix', 'type' => 'select', 'options' => document_request_suffix_options(), 'required' => false],
                ['name' => 'complete_address', 'label' => 'Complete Address', 'type' => 'text', 'required' => true],
                ['name' => 'date_of_birth', 'label' => 'Date of Birth (MM/DD/YYYY)', 'type' => 'date_mmddyyyy', 'required' => true],
                ['name' => 'age', 'label' => 'Age', 'type' => 'number', 'required' => true],
                ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['single', 'married', 'widowed', 'separated', 'annulled'], 'required' => true],
                ['name' => 'citizenship', 'label' => 'Citizenship', 'type' => 'text', 'required' => true],
                ['name' => 'contact_number', 'label' => 'Contact Number', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'textarea', 'required' => true],
            ],
            'required_uploads' => [
                ['key' => 'valid_id', 'label' => 'Valid ID'],
            ],
        ],
        'Certificate of Residency' => [
            'fields' => [
                ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
                ['name' => 'middle_name', 'label' => 'Middle Name', 'type' => 'text', 'required' => true],
                ['name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'required' => true],
                ['name' => 'suffix', 'label' => 'Suffix', 'type' => 'select', 'options' => document_request_suffix_options(), 'required' => false],
                ['name' => 'complete_address', 'label' => 'Complete Address', 'type' => 'text', 'required' => true],
                ['name' => 'date_of_birth', 'label' => 'Date of Birth (MM/DD/YYYY)', 'type' => 'date_mmddyyyy', 'required' => true],
                ['name' => 'age', 'label' => 'Age', 'type' => 'number', 'required' => true],
                ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['single', 'married', 'widowed', 'separated', 'annulled'], 'required' => true],
                ['name' => 'citizenship', 'label' => 'Citizenship', 'type' => 'text', 'required' => true],
                ['name' => 'contact_number', 'label' => 'Contact Number', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'length_of_residency_years', 'label' => 'Length of Residency (years)', 'type' => 'number', 'required' => true],
                ['name' => 'purpose_of_request', 'label' => 'Purpose of Request', 'type' => 'textarea', 'required' => true],
            ],
            'required_uploads' => [
                ['key' => 'valid_id', 'label' => 'Valid ID'],
                ['key' => 'proof_of_address', 'label' => 'Proof of Address'],
            ],
        ],
        'Solo Parent Certificate' => [
            'fields' => [
                ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
                ['name' => 'middle_name', 'label' => 'Middle Name', 'type' => 'text', 'required' => true],
                ['name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'required' => true],
                ['name' => 'suffix', 'label' => 'Suffix', 'type' => 'select', 'options' => document_request_suffix_options(), 'required' => false],
                ['name' => 'complete_address', 'label' => 'Complete Address', 'type' => 'text', 'required' => true],
                ['name' => 'date_of_birth', 'label' => 'Date of Birth (MM/DD/YYYY)', 'type' => 'date_mmddyyyy', 'required' => true],
                ['name' => 'age', 'label' => 'Age', 'type' => 'number', 'required' => true],
                ['name' => 'contact_number', 'label' => 'Contact Number', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'number_of_children', 'label' => 'Number of Children', 'type' => 'number', 'required' => true],
                ['name' => 'children_names', 'label' => 'Children Names (one per line)', 'type' => 'textarea', 'required' => true],
                ['name' => 'solo_parent_reason', 'label' => 'Reason', 'type' => 'select', 'options' => ['spouse has died', 'spouse is in jail/detention', 'legally separated/annulled', 'abandoned by spouse', 'unwed parent', 'others'], 'required' => true],
                ['name' => 'solo_parent_reason_other', 'label' => 'Reason (Others)', 'type' => 'text', 'required' => false, 'required_if' => ['field' => 'solo_parent_reason', 'equals' => 'others']],
            ],
            'required_uploads' => [
                ['key' => 'parent_valid_id', 'label' => 'Parent Valid ID'],
                ['key' => 'child_psa_birth_certificate', 'label' => 'Child PSA Birth Certificate'],
            ],
        ],
    ];
}

function document_request_definition_by_service_name($serviceName)
{
    $defs = document_request_definitions();
    return isset($defs[$serviceName]) ? $defs[$serviceName] : null;
}

function document_request_is_valid_mmddyyyy($value)
{
    if (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $value, $matches)) {
        return false;
    }

    $month = (int)$matches[1];
    $day = (int)$matches[2];
    $year = (int)$matches[3];
    return checkdate($month, $day, $year);
}

function document_request_readable_field_name($key)
{
    return ucwords(str_replace('_', ' ', $key));
}
