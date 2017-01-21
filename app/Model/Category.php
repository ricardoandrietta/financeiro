<?php

namespace App\Model;

use App\FinancialModel;

class Category extends FinancialModel
{
    protected $fillable = [
        'name',
        'status'
    ];

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

	public function find($id)
	{
		$category = parent::find($id);
		if (is_null($category))
			$category = parent::find(0);

		return $category;
    }
}
