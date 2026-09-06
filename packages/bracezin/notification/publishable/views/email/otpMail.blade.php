<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ config('app.name') }} OTP Verification</title>
	<style type="text/css">
		body {
			margin: 0;
			padding: 0;
			background-color: #f4f7fa;
			font-family: Arial, Helvetica, sans-serif;
			color: #1f2937;
		}

		table {
			border-collapse: collapse;
		}

		.email-wrapper {
			width: 100%;
			background-color: #f4f7fa;
			padding: 24px 12px;
		}

		.email-container {
			width: 100%;
			max-width: 600px;
			margin: 0 auto;
			background-color: #ffffff;
			border: 1px solid #dbe3ea;
			border-radius: 10px;
			overflow: hidden;
		}

		.email-header {
			background: linear-gradient(135deg, #2c9d72, #236f56);
			padding: 22px 28px;
			color: #ffffff;
		}

		.brand-name {
			margin: 0;
			font-size: 20px;
			font-weight: 700;
			letter-spacing: 0.3px;
		}

		.header-subtitle {
			margin: 8px 0 0;
			font-size: 14px;
			opacity: 0.95;
		}

		.email-body {
			padding: 28px;
		}

		.greeting {
			margin: 0 0 14px;
			font-size: 16px;
			font-weight: 600;
			color: #111827;
		}

		.message {
			margin: 0 0 16px;
			font-size: 15px;
			line-height: 1.6;
			color: #374151;
		}

		.otp-box {
			margin: 18px 0 20px;
			padding: 16px;
			text-align: center;
			background-color: #eef7f3;
			border: 1px dashed #2c9d72;
			border-radius: 8px;
		}

		.otp-label {
			margin: 0 0 8px;
			font-size: 12px;
			text-transform: uppercase;
			letter-spacing: 1px;
			color: #4b5563;
		}

		.otp-code {
			margin: 0;
			font-size: 34px;
			font-weight: 700;
			line-height: 1.1;
			letter-spacing: 8px;
			color: #124535;
		}

		.note {
			margin: 0;
			font-size: 14px;
			line-height: 1.6;
			color: #4b5563;
		}

		.validity {
			margin: 0 0 16px;
			font-size: 14px;
			line-height: 1.6;
			color: #065f46;
			font-weight: 600;
		}

		.email-footer {
			padding: 20px 28px 24px;
			border-top: 1px solid #e5e7eb;
			background-color: #fbfcfd;
		}

		.footer-text {
			margin: 0;
			font-size: 13px;
			line-height: 1.6;
			color: #6b7280;
		}

		@media only screen and (max-width: 600px) {
			.email-wrapper {
				padding: 12px 8px;
			}

			.email-body,
			.email-header,
			.email-footer {
				padding-left: 18px;
				padding-right: 18px;
			}

			.otp-code {
				font-size: 28px;
				letter-spacing: 6px;
			}
		}
	</style>
</head>
<body>
	<table class="email-wrapper" role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center">
				<table class="email-container" role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td class="email-header">
							<p class="brand-name">{{ config('app.name') }}</p>
							<p class="header-subtitle">One-Time Password (OTP) Verification</p>
						</td>
					</tr>
					<tr>
						<td class="email-body">
							<p class="greeting">Dear {{ $name }},</p>
							<p class="message">
								We received a request to sign in to your account. Please use the One-Time Password (OTP) below to continue on the {{ config('app.name') }} login page.
							</p>

							<div class="otp-box">
								<p class="otp-label">Your verification code</p>
								<p class="otp-code">{{ $otp }}</p>
							</div>

							<p class="validity">This OTP is valid for 30 minutes.</p>

							<p class="note">
								If you did not request this code, you can safely ignore this email.
							</p>
						</td>
					</tr>
					<tr>
						<td class="email-footer">
							<p class="footer-text">
								Regards,<br>
								{{ config('app.name') }} Team
							</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
