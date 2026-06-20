<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // Resident Management
    case VIEW_RESIDENTS = 'view_residents';
    case CREATE_RESIDENTS = 'create_residents';
    case EDIT_RESIDENTS = 'edit_residents';
    case DELETE_RESIDENTS = 'delete_residents';
    case APPROVE_RESIDENT_CHANGES = 'approve_resident_changes';
    
    // Complaint Management
    case VIEW_COMPLAINTS = 'view_complaints';
    case SUBMIT_COMPLAINTS = 'submit_complaints';
    case UPDATE_COMPLAINTS = 'update_complaints';
    case RESOLVE_COMPLAINTS = 'resolve_complaints';
    
    // Letter Management
    case VIEW_LETTERS = 'view_letters';
    case SUBMIT_LETTERS = 'submit_letters';
    case APPROVE_RT_LETTERS = 'approve_rt_letters';
    case APPROVE_RW_LETTERS = 'approve_rw_letters';
    case COMPLETE_LETTERS = 'complete_letters';
    
    // Financial Management
    case VIEW_FINANCES = 'view_finances';
    case MANAGE_FINANCES = 'manage_finances';
    
    // Information Management
    case MANAGE_INFORMATION = 'manage_information';
    
    // System
    case MANAGE_SYSTEM = 'manage_system';
    case VIEW_AUDIT_LOGS = 'view_audit_logs';
}
