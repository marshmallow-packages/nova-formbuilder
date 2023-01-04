 <div @class([
     'px-2 pt-4 col-span-12' => $fullWidth,
     'px-8 pt-2 pb-4 md:col-start-2 md:col-span-10 ' => !$fullWidth,
     'flex-1' => !$sidebar,
     'relative h-auto ',
 ])>
     <div class="flex justify-between pb-2 border-b border-gray-100 md:col-span-3">
         <div class="md:px-4">
             <h3 class="mb-1 text-2xl font-bold leading-tight text-gray-800 md:text-3xl lastDot-primary-500">
                 {{ $title }}</h3>

             @if ($subtitle && $subtitle->isNotEmpty())
                 <p class="pt-1 font-sans font-semibold leading-none text-gray-600 text-md ">
                     {{ $subtitle }}
                 </p>
             @endif
             @if ($description && $description->isNotEmpty())
                 <p class="pt-4 pb-3 text-sm text-gray-700 ">
                     {{ $description ?? '' }}
                 </p>
             @endif
         </div>
     </div>
 </div>
