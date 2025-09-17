<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; use Illuminate\Support\Facades\DB;
return new class extends Migration { public function up(): void { Schema::table('admins', function(Blueprint $t){ $t->string('role',20)->default('admin')->after('password'); });
    // Promote first existing admin to superadmin if any
    $first = DB::table('admins')->orderBy('id')->first(); if($first){ DB::table('admins')->where('id',$first->id)->update(['role'=>'superadmin']); }
}
public function down(): void { Schema::table('admins', function(Blueprint $t){ $t->dropColumn('role'); }); } };