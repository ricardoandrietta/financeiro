<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionReferenceRequest;
use App\Model\Category;
use App\Model\TransactionReference;
use Illuminate\Support\Facades\Auth;
use Session;

class TransactionReferenceController extends Controller
{
    /**
     * @var Reference
     */
    private $reference;
    /**
     * @var Category
     */
    private $category;

    public function __construct(TransactionReference $reference, Category $category)
    {
        $this->reference = $reference;
        $this->category = $category;
    }

    public function index()
    {
        $accountId = Auth::user()->account->id;
        $references = $this->reference->findAll($accountId);
        return view('layouts.reference_index', compact('references'));
    }

    public function edit(TransactionReferenceRequest $request, $id)
    {
        $categories = $this->category->getCombo();
        $transactionReference = $this->reference->find($id);

        return view('layouts.reference_store', compact('categories', 'transactionReference'));
    }

    public function update(TransactionReferenceRequest $request, $id)
    {
        $category = $this->category->find($request->input('category'));
        $reference = $this->reference->find($id);
        $reference->category()->associate($category);
        $reference->save();

        Session::flash('success', trans('reference.messages.updated_successfully'));
        return redirect()->route('reference.index');
    }

    public function destroy($id)
    {
        $reference = $this->reference->find($id);
        $reference->delete();

        Session::flash('success', trans('reference.messages.deleted_successfully'));
        return redirect()->route('reference.index');
    }
}
