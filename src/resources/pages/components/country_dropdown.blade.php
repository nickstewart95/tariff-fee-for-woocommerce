<div class="tariff_fee_country_of_origin_field">
	<p class="form-field tariff_fee_country_of_origin_field">
		<label for="tariff_fee_country_of_origin">Country of origin:</label>
		<select style="" id="tariff_fee_country_of_origin" name="tariff_fee_country_of_origin" class="select short">
			@foreach($countries as $country_code => $country_name)
				<option value="{{ $country_code }}" {{ $country_code === $selected_country ? 'selected' : '' }}>{{ $country_name }}</option>
			@endforeach
		</select>
	</p>
</div>