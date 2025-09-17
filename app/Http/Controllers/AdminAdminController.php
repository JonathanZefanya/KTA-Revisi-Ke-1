<?php
namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAdminController extends Controller
{
    protected function ensureSuper(Request $request)
    {
        $me = $request->user('admin');
        abort_unless($me && $me->role === 'superadmin', 403, 'Hanya superadmin.');
        return $me;
    }

    public function index(Request $request)
    {
        $this->ensureSuper($request); // Only superadmin manage
        $admins = Admin::orderBy('created_at','desc')->get();
        return view('admin.admins.index', compact('admins'));
    }

    public function create(Request $request)
    {
        $this->ensureSuper($request);
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $this->ensureSuper($request);
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:150','unique:admins,email'],
            'password' => ['required','string','min:6'],
            'role' => ['required','in:admin,superadmin'],
        ]);
        if($data['role']==='superadmin' && Admin::where('role','superadmin')->count()===0){
            // first superadmin fine, else allow; no special extra rule
        }
        Admin::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'role'=>$data['role']
        ]);
        return redirect()->route('admin.admins.index')->with('success','Admin baru ditambahkan.');
    }

    public function edit(Request $request, Admin $admin)
    {
        $this->ensureSuper($request);
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $this->ensureSuper($request);
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:150','unique:admins,email,'.$admin->id],
            'password' => ['nullable','string','min:6'],
            'role' => ['required','in:admin,superadmin'],
        ]);
        // Prevent demoting last superadmin
        if($admin->role==='superadmin' && $data['role']!=='superadmin'){
            if(Admin::where('role','superadmin')->where('id','!=',$admin->id)->count()===0){
                return back()->with('error','Tidak bisa menurunkan role superadmin terakhir.');
            }
        }
        $update = [ 'name'=>$data['name'], 'email'=>$data['email'], 'role'=>$data['role'] ];
        if(!empty($data['password'])){ $update['password']=Hash::make($data['password']); }
        $admin->update($update);
        return redirect()->route('admin.admins.index')->with('success','Admin diperbarui.');
    }

    public function destroy(Request $request, Admin $admin)
    {
        $this->ensureSuper($request);
        if($request->user('admin')->id === $admin->id){
            return back()->with('error','Tidak bisa menghapus akun sendiri.');
        }
        if($admin->role==='superadmin' && Admin::where('role','superadmin')->where('id','!=',$admin->id)->count()===0){
            return back()->with('error','Tidak bisa menghapus superadmin terakhir.');
        }
        $admin->delete();
        return back()->with('success','Admin dihapus.');
    }
}