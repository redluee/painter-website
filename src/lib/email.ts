import nodemailer from 'nodemailer';

interface ContactSubmission {
  name: string;
  email: string;
  subject: string;
  message: string;
}

let transporter: any | null = null;

function getTransporter() {
  if (!transporter) {
    const host = import.meta.env.SMTP_HOST as string;
    const port = parseInt(import.meta.env.SMTP_PORT || '587', 10);
    const user = import.meta.env.SMTP_USER as string;
    const pass = import.meta.env.SMTP_PASS as string;

    if (!host || !user || !pass) {
      throw new Error('SMTP environment variables (SMTP_HOST, SMTP_USER, SMTP_PASS) are required for email sending');
    }

    transporter = nodemailer.createTransport({
      host,
      port,
      secure: port === 465,
      auth: { user, pass },
    });
  }
  return transporter;
}

function getNotificationEmail(): string {
  return (import.meta.env.NOTIFICATION_EMAIL as string) || 'info@sebastiaanpeters.nl';
}

export async function sendContactNotification(
  submission: ContactSubmission,
): Promise<void> {
  const subjectLabels: Record<string, string> = {
    offerte: 'Offerte aanvraag',
    vraag: 'Algemene vraag',
    anders: 'Anders',
  };

  await getTransporter().sendMail({
    from: `"Contactformulier" <${import.meta.env.SMTP_USER as string}>`,
    replyTo: submission.email,
    to: getNotificationEmail(),
    subject: `Nieuwe ${subjectLabels[submission.subject] || submission.subject} van ${submission.name}`,
    html: `
      <h2>Nieuw contactformulier bericht</h2>
      <table>
        <tr><td><strong>Naam:</strong></td><td>${submission.name}</td></tr>
        <tr><td><strong>E-mail:</strong></td><td>${submission.email}</td></tr>
        <tr><td><strong>Onderwerp:</strong></td><td>${subjectLabels[submission.subject] || submission.subject}</td></tr>
      </table>
      <h3>Bericht:</h3>
      <p>${submission.message.replace(/\n/g, '<br>')}</p>
    `,
  });
}
