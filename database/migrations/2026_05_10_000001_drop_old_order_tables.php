<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }

    public function down(): void
    {
        // Data kosong — tidak bisa rollback. Restore manual jika perlu.
    }
};
