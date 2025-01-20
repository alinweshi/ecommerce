<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\UploadFileService;
use Exception;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Livewire\Forms\UserForm;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RegisterFormComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $image;
    public $selectedUser_id = '';


    // public UserForm $form;
    protected function rules()
    {
        return [
            'first_name' => 'required|min:5|max:50',
            'last_name' => 'required|min:5|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            // |size:11|unique:users,phone
            // 'image'=>'required|image|mimes:png,jpg,jpeg|max:2048',
            'password' => [
                'required',
                Password::min(8)
                    // ->letters()
                    // ->mixedCase()
                    // ->numbers()
                    // ->symbols()
                    // ->uncompromised(),
            ],
        ];
    }

    public function createUser(UploadFileService $fileUploadService)
    {
        $validated = $this->validate();
        $validated['password'] = Hash::make($validated['password']);
        // dd( $fileUploadService->uploadFile($this->image));

        try {
            // Upload image file
            if ($this->image) {
                // $validated['image'] = $this->image->store('images');
                $validated['image'] = $fileUploadService->uploadFile($this->image);
            }
        } catch (Exception $e) {
            // Add error to 'image' field and log the exception
            $this->addError('image', 'File upload failed. Please try again.');
            \Log::error("File upload error: " . $e->getMessage());
            return; // Exit function to prevent user creation if upload fails
        }

        $user = User::create($validated);

        $this->dispatch('user-created', $user);
        // Reset form fields and flash a message based on success or failure
        $this->reset(['first_name', 'last_name', 'email', 'phone', 'password', 'image']);
        session()->flash($user ? 'success' : 'error', $user ? 'User Created Successfully' : 'User Not Created');
    }

    public function render()
    {
        return view('livewire.register-form-component', [
            'isTemporaryFile' => $this->image instanceof TemporaryUploadedFile,
            'users' => User::paginate(8),
        ]);
    }
}