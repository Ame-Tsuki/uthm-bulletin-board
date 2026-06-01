public function up()
{
    Schema::table('announcements', function (Blueprint $table) {
        if (!Schema::hasColumn('announcements', 'is_featured')) {
            $table->boolean('is_featured')->default(false);
        }
        if (!Schema::hasColumn('announcements', 'featured_image')) {
            $table->string('featured_image')->nullable();
        }
        if (!Schema::hasColumn('announcements', 'featured_order')) {
            $table->integer('featured_order')->nullable();
        }
        if (!Schema::hasColumn('announcements', 'featured_at')) {
            $table->timestamp('featured_at')->nullable();
        }
    });
}

public function down()
{
    Schema::table('announcements', function (Blueprint $table) {
        $table->dropColumn(['is_featured', 'featured_image', 'featured_order', 'featured_at']);
    });
}