<?php

namespace App\Http\Controllers;

use App\Events\RefundRequestCreated;
use App\Models\Refund;
use App\Notifications\NewRefundRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     */ public function index(Request $request): View|JsonResponse
    {

        if ($request->ajax()) {

            //dd($request);

            //$data = Refund::query();
            $data = Refund::select('id', 'libraryCard', 'firstName', 'lastName', 'phone', 'refund_status')->orderBy('refund_status', 'asc');


            // Implement searching based on request input
            // if ($search = $request->get('search')['value']) {
            //     $data->where(function ($query) use ($search) {
            //         $query->where(DB::raw('LOWER(libraryCard)'), 'LIKE', '%' . strtolower($search) . '%')
            //             ->orWhere(DB::raw('LOWER(firstName)'), 'LIKE', '%' . strtolower($search) . '%')
            //             ->orWhere(DB::raw('LOWER(lastName)'), 'LIKE', '%' . strtolower($search) . '%')
            //             ->orWhere(DB::raw('LOWER(phone)'), 'LIKE', '%' . strtolower($search) . '%');
            //     });
            // }


            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('action', function ($row) {
                //     $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="View" class="me-1 btn btn-info btn-sm showRefund"><i class="fa-regular fa-eye"></i> View</a>';
                //     $btn = $btn . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editRefund"><i class="fa-regular fa-pen-to-square"></i> Edit</a>';
                //     $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteRefund"><i class="fa-solid fa-trash"></i> Delete</a>';
                //     return $btn;
                // })
                // ->rawColumns(['action'])
                ->make(true);
        }

        return view('refunds.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        // Begin transaction
        DB::beginTransaction();

        try {
            $affectedRows = Refund::where("id", $request->refund_id)->update(["notes" => $request->refund_notes]);
            // Commit the transaction
            DB::commit();
            return response()->json(['success' => 'Refund saved successfully. '], 200);
        } catch (\Exception $e) {
            // Rollback the transaction if anything goes wrong
            DB::rollBack();
            // Optionally log the error
            Log::error('Update refund error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating the refund.'], 500);
        }
    }


    public function update(Request $request, $id)
    {
        // Find the refund record by ID
        $refund = Refund::findOrFail($id);

        // Validate incoming data
        $validator = Validator::make($request->all(), [
            // 'libraryCard' => 'required|string|max:255',
            // 'firstName' => 'required|string|max:255',
            // 'lastName' => 'required|string|max:255',
            // 'phone' => 'required|string|max:15',
            'refund_status' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Begin transaction
        DB::beginTransaction();

        try {

            // Update the refund record
            $refund->update([
                // 'libraryCard' => $request->libraryCard,
                // 'firstName' => $request->firstName,
                // 'lastName' => $request->lastName,
                // 'phone' => $request->phone,
                'refund_status' => $request->refund_status,
            ]);

            // Commit the transaction
            DB::commit();

            // Dispatch the event
            event(new RefundRequestCreated($refund));

            return response()->json(['success' => true, 'refund' => $refund], 200);
        } catch (\Exception $e) {
            // Rollback the transaction if anything goes wrong
            DB::rollBack();
            // Optionally log the error
            Log::error('Update refund error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating the refund.'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Refund  $Refund
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $refund = Refund::find($id);

        return response()->json([
            'id' => $refund->id,
            'libraryCard' => $refund->libraryCard,
            'fullname' => $refund->firstName . ' ' . $refund->lastName,
            'phone' => $refund->phone,
            'notes' => $refund->notes,
            'amount'  => $refund->amount,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Refund  $Refund
     * @return \Illuminate\Http\Response
     */
    public function edit($id): JsonResponse
    {
        $refund = Refund::find($id);

        return response()->json([
            'id' => $refund->id,
            'libraryCard' => $refund->libraryCard,
            'fullname' => $refund->firstName . ' ' . $refund->lastName,
            'notes' => $refund->notes,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Refund  $Refund
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): JsonResponse
    {
        Refund::find($id)->delete();

        return response()->json(['success' => 'Refund deleted successfully.']);
    }
}
