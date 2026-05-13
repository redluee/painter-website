import nodemailer from 'nodemailer';

const transporter = nodemailer.createTransport({
  host: import.meta.env.SMTP_HOST as string,
  port: parseInt(import.meta.env.SMTP_PORT || '587', 10),
  secure: parseInt(import.meta.env.SMTP_PORT || '587', 10) === 465,
  auth: {
    user: import.meta.env.SMTP_USER as string,
    pass: import.meta.env.SMTP_PASS as string,
  },
});

const notificationEmail =
  (import.meta.env.NOTIFICATION_EMAIL as string) || 'info@sebastiaanpeters.nl';

interface ContactSubmission {
  name: string;
  email: string;
  subject: string;
  message: string;
}

export async function sendContactNotification(
  submission: ContactSubmission,
): Promise<void> {
  const subjectLabels: Record<string, string> = {
    offerte: 'Offerte aanvraag',
    vraag: 'Algemene vraag',
    anders: 'Anders',
  };

  await transporter.sendMail({
    from: `"Contactformulier" <${import.meta.env.SMTP_USER as string}>`,
    replyTo: submission.email,
    to: notificationEmail,
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
