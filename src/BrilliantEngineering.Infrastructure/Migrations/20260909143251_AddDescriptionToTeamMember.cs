using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace BrilliantEngineering.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class AddDescriptionToTeamMember : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "Description",
                table: "TeamMembers",
                type: "nvarchar(max)",
                nullable: false,
                defaultValue: "");

            migrationBuilder.AddColumn<string>(
                name: "DescriptionAr",
                table: "TeamMembers",
                type: "nvarchar(max)",
                nullable: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "DescriptionAr",
                table: "TeamMembers");

            migrationBuilder.DropColumn(
                name: "Description",
                table: "TeamMembers");
        }
    }
}
