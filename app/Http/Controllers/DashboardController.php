use App\Models\Announcement;

public function index()
{
    $announcements = Announcement::latest()->take(5)->get();

    return view('dashboard', compact('announcements'));
}