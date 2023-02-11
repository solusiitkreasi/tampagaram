<table class="table table-striped mb-5 bbcodes-table" style="border: 1px solid #0000005a;">
    <thead>
      <tr>
        <th scope="col">Short Code</th>
        <th scope="col">Meaning</th>
      </tr>
    </thead>
    <tbody>

        <tr>
            <td>
              {customer_name}
            </td>
            <th scope="row">
              Customer Name
            </th>
        </tr>

        @if ($templateInfo->mail_type == 'room booking')
        <tr>
            <td>
              {booking_number}
            </td>
            <th scope="row">
              Booking Number
            </th>
        </tr>
        <tr>
            <td>
              {booking_date}
            </td>
            <th scope="row">
              Booking Date
            </th>
        </tr>
        <tr>
            <td>
              {number_of_night}
            </td>
            <th scope="row">
              Number of Nights
            </th>
        </tr>
        <tr>
            <td>
              {check_in_date}
            </td>
            <th scope="row">
              Check in Date
            </th>
        </tr>
        <tr>
            <td>
              {check_out_date}
            </td>
            <th scope="row">
              Check out Date
            </th>
        </tr>
        <tr>
            <td>
              {number_of_guests}
            </td>
            <th scope="row">
              Number of Guests
            </th>
        </tr>
        <tr>
            <td>
              {room_name}
            </td>
            <th scope="row">
              Room Name
            </th>
        </tr>
        <tr>
            <td>
              {room_rent}
            </td>
            <th scope="row">
              Room Rent
            </th>
        </tr>
        <tr>
            <td>
              {room_type}
            </td>
            <th scope="row">
              Room Type
            </th>
        </tr>
        <tr>
            <td>
              {room_amenities}
            </td>
            <th scope="row">
              Room Amenities
            </th>
        </tr>
        @endif


        @if ($templateInfo->mail_type == 'package booking')
        <tr>
            <td>
              {booking_number}
            </td>
            <th scope="row">
              Booking Number
            </th>
        </tr>
        <tr>
            <td>
              {package_name}
            </td>
            <th scope="row">
              Package Name
            </th>
        </tr>
        <tr>
            <td>
              {package_price}
            </td>
            <th scope="row">
              Package Price
            </th>
        </tr>
        <tr>
            <td>
              {number_of_visitors}
            </td>
            <th scope="row">
              Number of Visitors
            </th>
        </tr>
        @endif

        @if ($templateInfo->mail_type == 'verify email')
        <tr>
            <td>
              {customer_username}
            </td>
            <th scope="row">
              Username
            </th>
        </tr>
        <tr>
            <td>
              {verification_link}
            </td>
            <th scope="row">
              Verification Link
            </th>
        </tr>
        @endif

        @if ($templateInfo->mail_type == 'reset password')
        <tr>
            <td>
              {password_reset_link}
            </td>
            <th scope="row">
              Password Reset Link
            </th>
        </tr>
        @endif

        <tr>
          <td>
            {website_title}
          </td>
          <th scope="row">
            Website Title
          </th>
        </tr>

    </tbody>
</table>
