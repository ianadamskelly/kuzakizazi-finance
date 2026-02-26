<x-mail::message>
# New Support Request

A new support request has been submitted from the EduFinance Pro application.

**From:** {{ $data['name'] }}
**Email:** {{ $data['email'] }}

**Subject:** {{ $data['subject'] }}

---

**Message:**

{{ $data['message'] }}

</x-mail::message>
