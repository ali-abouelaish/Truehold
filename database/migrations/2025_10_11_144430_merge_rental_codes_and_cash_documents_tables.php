<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add cash document fields to rental_codes table
        Schema::table('rental_codes', function (Blueprint $table) {
            // Cash Document Fields - Reserved Section
            $table->json('contact_images')->nullable()->after('notes')->comment('Reserved: Up to 4 contact document images');
            $table->string('client_id_image')->nullable()->after('contact_images')->comment('Reserved: Client ID document');
            $table->string('cash_receipt_image')->nullable()->after('client_id_image')->comment('Reserved: Cash envelope or transfer receipt');
            $table->enum('cash_document_status', ['pending', 'approved', 'rejected'])->nullable()->after('cash_receipt_image')->comment('Reserved: Cash document review status');
            $table->timestamp('cash_document_submitted_at')->nullable()->after('cash_document_status')->comment('Reserved: When cash documents were submitted');
            $table->timestamp('cash_document_reviewed_at')->nullable()->after('cash_document_submitted_at')->comment('Reserved: When cash documents were reviewed');
            $table->foreignId('cash_document_reviewed_by')->nullable()->constrained('users')->onDelete('set null')->after('cash_document_reviewed_at')->comment('Reserved: Who reviewed the cash documents');
        });

        // Migrate existing cash documents data to rental_codes
        $this->migrateCashDocumentsData();

        // Drop the cash_documents table
        Schema::dropIfExists('cash_documents');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate cash_documents table
        Schema::create('cash_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->json('contact_images')->nullable();
            $table->string('client_id_image')->nullable();
            $table->string('cash_receipt_image')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Remove cash document fields from rental_codes
        Schema::table('rental_codes', function (Blueprint $table) {
            $table->dropColumn([
                'contact_images',
                'client_id_image', 
                'cash_receipt_image',
                'cash_document_status',
                'cash_document_submitted_at',
                'cash_document_reviewed_at',
                'cash_document_reviewed_by'
            ]);
        });
    }

    /**
     * Migrate existing cash documents data to rental_codes
     */
    private function migrateCashDocumentsData(): void
    {
        // Get all cash documents
        $cashDocuments = DB::table('cash_documents')->get();
        
        foreach ($cashDocuments as $cashDoc) {
            // Find corresponding rental code by client_id
            $rentalCode = DB::table('rental_codes')
                ->where('client_id', $cashDoc->client_id)
                ->first();
            
            if ($rentalCode) {
                // Update the rental code with cash document data
                DB::table('rental_codes')
                    ->where('id', $rentalCode->id)
                    ->update([
                        'contact_images' => $cashDoc->contact_images,
                        'client_id_image' => $cashDoc->client_id_image,
                        'cash_receipt_image' => $cashDoc->cash_receipt_image,
                        'cash_document_status' => $cashDoc->status,
                        'cash_document_submitted_at' => $cashDoc->submitted_at,
                        'cash_document_reviewed_at' => $cashDoc->reviewed_at,
                        'cash_document_reviewed_by' => $cashDoc->reviewed_by,
                    ]);
            }
        }
    }
};