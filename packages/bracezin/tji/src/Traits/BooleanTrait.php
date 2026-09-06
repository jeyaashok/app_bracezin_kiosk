<?php

namespace Tji\Traits;

/******* Available Index *********
 * Boolean Query Funtions:-
 * IsSysAdmin()
 * IsNotSysAdmin()
 * IsActive()
 * IsNotActive()
 * IsPrimary()
 * IsNotPrimary()
 * IsHidden()
 * IsNotHidden()
 * IsSysImage()
 * IsNotSysImage()
 * IsSysFile()
 * IsNotSysFile()
 * IsCompleted()
 * IsNotCompleted()
 * IsDelivered()
 * IsNotDelivered()
 * IsEditable()
 * IsNotEditable()
 * IsCancelled()
 * IsNotCancelled()
 * IsTaxable()
 * IsNotTaxable()
 * IsReceived()
 * IsNotReceived()
 * IsOpenNewTab()
 * IsNotOpenNewTab()
 *
 * SysAdminFilter($input = null)
 * ActiveFilter($input = null)
 * PrimaryFilter($input = null)
 * HiddenFilter($input = null)
 * SysImageFilter($input = null)
 * SysFileFilter($input = null)
 * CompletedFilter($input = null)
 * DeliveredFilter($input = null)
 * EditableFilter($input = null)
 * CancelledFilter($input = null)
 * TaxableFilter($input = null)
 * ReceivedFilter($input = null)
 * OpenNewTabFilter($input = null)
 ******* Index *********/

trait BooleanTrait {

////////////***********  All BOOLEAN Checks *******************////////////
	/** Boolean SysAdmin Check */
	public function scopeIsSysAdmin($query) {
		return $query->where('is_sysAdmin', true);
	}

	public function scopeIsNotSysAdmin($query) {
		return $query->where('is_sysAdmin', false);
	}

	/** Boolean Active Check */
	public function scopeIsActive($query) {
		return $query->where('is_active', true);
	}

	public function scopeIsNotActive($query) {
		return $query->where('is_active', false);
	}

	/** Boolean Primary Check */
	public function scopeIsPrimary($query) {
		return $query->where('is_primary', true);
	}

	public function scopeIsNotPrimary($query) {
		return $query->where('is_primary', false);
	}

	/** Boolean Hidden Check */
	public function scopeIsHidden($query) {
		return $query->where('is_hidden', true);
	}

	public function scopeIsNotHidden($query) {
		return $query->where('is_hidden', false);
	}

	/** Boolean SysImage Check */
	public function scopeIsSysImage($query) {
		return $query->where('is_sysImage', true);
	}

	public function scopeIsNotSysImage($query) {
		return $query->where('is_sysImage', false);
	}

	/** Boolean SysFile Check */
	public function scopeIsSysFile($query) {
		return $query->where('is_sysFile', true);
	}

	public function scopeIsNotSysFile($query) {
		return $query->where('is_sysFile', false);
	}

	/** Boolean Taxable Check */
	public function scopeIsTaxable($query) {
		return $query->where('is_taxable', true);
	}

	public function scopeIsNotTaxable($query) {
		return $query->where('is_taxable', false);
	}

	/** Boolean Cancelled Check */
	public function scopeIsCancelled($query) {
		return $query->where('is_cancelled', true);
	}

	public function scopeIsNotCancelled($query) {
		return $query->where('is_cancelled', false);
	}

	/** Boolean Completed Check */
	public function scopeIsCompleted($query) {
		return $query->where('is_completed', true);
	}

	public function scopeIsNotCompleted($query) {
		return $query->where('is_completed', false);
	}

	/** Boolean Delivered Check */
	public function scopeIsDelivered($query) {
		return $query->where('is_delivered', true);
	}

	public function scopeIsNotDelivered($query) {
		return $query->where('is_delivered', false);
	}

	/** Boolean Editable Check */
	public function scopeIsEditable($query) {
		return $query->where('is_editable', true);
	}

	public function scopeIsNotEditable($query) {
		return $query->where('is_editable', false);
	}

	/** Boolean Received Check */
	public function scopeIsReceived($query) {
		return $query->where('is_received', true);
	}

	public function scopeIsNotReceived($query) {
		return $query->where('is_received', false);
	}

	/** Boolean Mandatory Check */
	public function scopeIsMandatory($query) {
		return $query->where('is_mandatory', true);
	}

	public function scopeIsNotMandatory($query) {
		return $query->where('is_mandatory', false);
	}

	/** Boolean OpenNewTab Check */
	public function scopeIsOpenNewTab($query) {
		return $query->where('is_open_new_tab', true);
	}

	public function scopeIsNotOpenNewTab($query) {
		return $query->where('is_open_new_tab', false);
	}

	/** Boolean Display Check */
	public function scopeIsDisplay($query) {
		return $query->where('is_display', true);
	}

	public function scopeIsNotDisplay($query) {
		return $query->where('is_display', false);
	}

}
