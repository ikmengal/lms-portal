<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
    <div class="grid sm:grid-cols-2 gap-6">
        <div class="sm:col-span-2">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Course Title <span class="text-red-500">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $course->title ?? '') }}" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('title') border-red-300 @enderror"
                placeholder="e.g. Python for Data Science & Machine Learning Bootcamp" />
            @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea id="description" name="description" rows="5"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('description') border-red-300 @enderror"
                placeholder="What will students learn?">{{ old('description', $course->description ?? '') }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="course_category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
            <select id="course_category_id" name="course_category_id" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition bg-white @error('course_category_id') border-red-300 @enderror">
                <option value="">Select category...</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('course_category_id', $course->course_category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('course_category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="course_level_id" class="block text-sm font-medium text-gray-700 mb-1">Level <span class="text-red-500">*</span></label>
            <select id="course_level_id" name="course_level_id" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition bg-white @error('course_level_id') border-red-300 @enderror">
                <option value="">Select level...</option>
                @foreach($levels as $lvl)
                    <option value="{{ $lvl->id }}" @selected(old('course_level_id', $course->course_level_id ?? '') == $lvl->id)>{{ $lvl->name }}</option>
                @endforeach
            </select>
            @error('course_level_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Duration (hours) <span class="text-red-500">*</span></label>
            <input type="number" id="duration_hours" name="duration_hours" min="0" max="2000" step="1" value="{{ old('duration_hours', $course->duration_hours ?? 0) }}" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('duration_hours') border-red-300 @enderror" />
            @error('duration_hours')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (USD) <span class="text-red-500">*</span></label>
            <input type="number" id="price" name="price" min="0" max="99999.99" step="0.01" value="{{ old('price', $course->price ?? '0.00') }}" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('price') border-red-300 @enderror" />
            <p class="mt-1 text-xs text-gray-400">Set 0 for a free course.</p>
            @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        @role('admin')
        <div>
            <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-1">Instructor</label>
            <select id="instructor_id" name="instructor_id"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition bg-white @error('instructor_id') border-red-300 @enderror">
                <option value="">No instructor</option>
                @foreach($instructors as $instructor)
                    <option value="{{ $instructor->id }}" @selected(old('instructor_id', $course->instructor_id ?? null) == $instructor->id)>{{ $instructor->name }}</option>
                @endforeach
            </select>
            @error('instructor_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        @endrole

        <div x-data="{ preview: '{{ isset($course) && $course->thumbnail ? asset('storage/' . $course->thumbnail) : null }}' }">
            <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
            <div class="flex items-center gap-3">
                <div class="w-24 h-16 bg-gray-50 border border-dashed border-gray-200 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                    <template x-if="preview"><img :src="preview" class="w-full h-full object-cover"></template>
                    <template x-if="!preview">
                        <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21z"/></svg>
                    </template>
                </div>
                <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                    Upload Image
                    <input type="file" name="thumbnail" accept=".jpg,.jpeg,.png,.webp" class="hidden"
                        @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                </label>
            </div>
            @error('thumbnail')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
        <a href="{{ route('admin.courses.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition">Cancel</a>
        <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
            {{ isset($course) ? 'Save Changes' : 'Create Course' }}
        </button>
    </div>
</div>
